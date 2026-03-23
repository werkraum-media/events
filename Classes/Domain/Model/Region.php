<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Domain\Model;

/*
 *
 * This file is part of the "DD Events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Dirk Koritnik <koritnik@werkraum-media.de>
 *
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Region extends AbstractEntity
{
    protected string $title = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param int<-1, max> $languageUid
     */
    public function setLanguageUid(int $languageUid): void
    {
        $this->_languageUid = $languageUid;
    }

    /**
     * @return int<-1, max>|null
     */
    public function getLanguageUid(): ?int
    {
        return $this->_languageUid;
    }
}
