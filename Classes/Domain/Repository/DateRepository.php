<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Repository;

use DateTimeImmutable;
use DateTimeZone;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use UnexpectedValueException;
use WerkraumMedia\Events\Domain\Model\Dto\DateDemand;
use WerkraumMedia\Events\Service\CategoryService;

final class DateRepository extends Repository
{
    public function __construct(
        private readonly Context $context,
        private readonly ConnectionPool $connectionPool,
    ) {
        parent::__construct();
    }

    public function findByUids(string $uids): QueryResult
    {
        $uids = explode(',', $uids);
        $query = $this->createQuery();
        $query->matching(
            $query->in('uid', $uids)
        );
        return $query->execute();
    }

    public function findByDemand(DateDemand $demand): QueryResult
    {
        $query = $this->createDemandQuery($demand);
        return $query->execute();
    }

    protected function createDemandQuery(DateDemand $demand): QueryInterface
    {
        $query = $this->createQuery();
        $constraints = [
            $this->createEventConstraint($query),
        ];

        $categoriesConstraint = $this->createCategoryConstraint($query, $demand);
        if ($categoriesConstraint instanceof ConstraintInterface) {
            $constraints['categories'] = $categoriesConstraint;
        }

        if ($demand->getFeatures() !== []) {
            $constraints['features'] = $this->createFeaturesConstraint($query, $demand);
        }

        if ($demand->getLocations() !== []) {
            $constraints['locations'] = $this->createLocationConstraint($query, $demand);
        }

        if ($demand->getOrganizers() !== []) {
            $constraints['organizer'] = $query->in('event.organizer', $demand->getOrganizers());
        }

        if ($demand->getRegion() !== '') {
            $constraints['region'] = $query->equals('event.region', $demand->getRegion());
        }

        if ($demand->getHighlight() !== false) {
            $constraints['highlight'] = $query->equals('event.highlight', $demand->getHighlight());
        }

        if ($demand->getSearchword() !== '') {
            $constraints['searchword'] = $this->getSearchwordConstraint($query, $demand);
        }

        if ($demand->getUserCategories() !== []) {
            $constraints['userCategories'] = $query->in('event.categories.uid', $demand->getUserCategories());
        }

        $timingConstraint = $this->createTimingConstraint($query, $demand);
        if ($timingConstraint instanceof ConstraintInterface) {
            $constraints['timing'] = $timingConstraint;
        }

        if ($demand->shouldShowFromNow() || $demand->shouldShowFromMidnight()) {
            $now = $this->getNow();

            if ($demand->shouldShowFromMidnight()) {
                $now = $now->modify('midnight');
            }

            $constraints['nowAndFuture'] = $query->logicalOr(
                $query->greaterThanOrEqual('start', $now),
                $query->greaterThanOrEqual('end', $now),
            );
        } elseif ($demand->shouldShowUpcoming()) {
            $now = $this->getNow();

            $constraints['future'] = $query->logicalAnd(
                $query->greaterThan('start', $now),
                $query->logicalOr(
                    $query->equals('end', 0),
                    $query->greaterThan('end', $now),
                ),
            );
        }

        if ($demand->getLimit() !== '') {
            $query->setLimit((int)$demand->getLimit());
        }

        $query->matching($query->logicalAnd(... $constraints));

        if ($demand->getSortBy() && $demand->getSortOrder()) {
            $query->setOrderings([$demand->getSortBy() => $demand->getSortOrder()]);
        }

        $callback = $demand->getQueryCalback();
        if ($callback !== '') {
            $params = ['query' => &$query];
            GeneralUtility::callUserFunction($callback, $params, $this);
        }

        return $query;
    }

    private function getSearchwordConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ConstraintInterface {
        $fieldsToSearch = [
            'event.title',
            'event.teaser',
            'event.categories.title',
            'event.location.name',
            'event.organizer.name',
        ];

        $wordsToSearch = $demand->getSynonymsForSearchword();
        $wordsToSearch[] = $demand->getSearchword();
        $constraints = [];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_date');

        foreach ($wordsToSearch as $word) {
            foreach ($fieldsToSearch as $field) {
                $constraints[] = $query->like($field, '%' . $queryBuilder->escapeLikeWildcards($word) . '%');
            }
        }

        return $query->logicalOr(... $constraints);
    }

    protected function createCategoryConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ?ConstraintInterface {
        $categories = $demand->getCategories();
        if ($categories === '') {
            return null;
        }
        $constraints = [];

        if ($demand->getIncludeSubCategories()) {
            $categories = GeneralUtility::makeInstance(CategoryService::class)
                ->getChildrenCategories($categories)
            ;
        }

        $categories = GeneralUtility::intExplode(',', $categories, true);
        foreach ($categories as $category) {
            $constraints[] = $query->contains('event.categories', $category);
        }

        if ($constraints === []) {
            return null;
        }

        if ($demand->getCategoryCombination() === 'or') {
            return $query->logicalOr(... $constraints);
        }

        return $query->logicalAnd(... $constraints);
    }

    private function createTimingConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ?ConstraintInterface {
        // Dates might have end of 0 if only start exists.

        if ($demand->getStartObject() !== null && $demand->getEndObject() === null) {
            return $query->logicalOr(
                $query->greaterThanOrEqual('start', $demand->getStartObject()),
                $query->greaterThanOrEqual('end', $demand->getStartObject()),
            );
        }

        if ($demand->getStartObject() === null && $demand->getEndObject() !== null) {
            return $query->logicalOr(
                $query->logicalAnd(
                    $query->lessThanOrEqual('end', $demand->getEndObject()),
                    $query->greaterThan('end', 0),
                ),
                $query->lessThanOrEqual('start', $demand->getEndObject()),
            );
        }

        if ($demand->getStartObject() !== null && $demand->getEndObject() !== null) {
            return $query->logicalOr(
                $query->logicalAnd(
                    $query->logicalOr(
                        $query->greaterThanOrEqual('start', $demand->getStartObject()),
                        $query->greaterThanOrEqual('end', $demand->getStartObject()),
                    ),
                    $query->logicalOr(
                        $query->lessThanOrEqual('start', $demand->getEndObject()),
                        $query->logicalAnd(
                            $query->lessThanOrEqual('end', $demand->getEndObject()),
                            $query->greaterThan('end', 0),
                        ),
                    ),
                ),
                $query->logicalAnd(
                    $query->lessThanOrEqual('start', $demand->getStartObject()),
                    $query->greaterThanOrEqual('end', $demand->getEndObject()),
                ),
            );
        }

        return null;
    }

    private function createFeaturesConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ConstraintInterface {
        $constraints = [];

        foreach ($demand->getFeatures() as $feature) {
            $constraints[] = $query->contains('event.features', $feature);
        }

        return $query->logicalAnd(... $constraints);
    }

    private function createLocationConstraint(
        QueryInterface $query,
        DateDemand $demand
    ): ConstraintInterface {
        $locations = $demand->getLocations();
        $uidsToResolve = $locations;

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_events_domain_model_location');
        $queryBuilder->select('children');
        $queryBuilder->from('tx_events_domain_model_location');

        // Loop as resolved uids might have further children which need to be resolved as well.
        do {
            $concreteQueryBuilder = clone $queryBuilder;
            $concreteQueryBuilder->where($concreteQueryBuilder->expr()->in(
                'uid',
                $concreteQueryBuilder->createNamedParameter($uidsToResolve, Connection::PARAM_INT_ARRAY)
            ));

            foreach ($concreteQueryBuilder->executeQuery()->fetchFirstColumn() as $newUids) {
                if (is_string($newUids) === false) {
                    $newUids = '';
                }
                $newUids = GeneralUtility::intExplode(',', $newUids, true);
                $uidsToResolve = array_diff($newUids, $locations);
                $locations = array_merge($locations, $uidsToResolve);
            }
        } while ($uidsToResolve !== []);

        return $query->in('event.location', $locations);
    }

    public function findSearchWord(string $search): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tx_events_domain_model_date');

        $queryBuilder = $connection->createQueryBuilder();

        $statement = $queryBuilder
            ->select('*')
            ->from('tx_events_domain_model_date')
            ->join(
                'tx_events_domain_model_date',
                'tx_events_domain_model_event',
                'event',
                $queryBuilder->expr()->eq(
                    'tx_events_domain_model_date.event',
                    $queryBuilder->quoteIdentifier('event.uid')
                )
            )->where(
                $queryBuilder->expr()->like('event.title', $queryBuilder->createNamedParameter('%' . $search . '%'))
            )->orderBy('tx_events_domain_model_date.start')
        ;

        return $statement->executeQuery()->fetchAllAssociative();
    }

    private function createEventConstraint(
        QueryInterface $query
    ): ConstraintInterface {
        return $query->logicalAnd(
            // Use sub property to trigger join and pulling in event table constraints (hidden)
            $query->logicalNot($query->equals('event.uid', null))
        );
    }

    private function getNow(): DateTimeImmutable
    {
        $now = $this->context->getPropertyFromAspect(
            'date',
            'full',
            new DateTimeImmutable()
        );

        if (!$now instanceof DateTimeImmutable) {
            throw new UnexpectedValueException(
                'Could not retrieve now as DateTimeImmutable, got "' . gettype($now) . '".',
                1639382648
            );
        }

        $now = $now->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $now;
    }
}
