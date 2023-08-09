<?php

declare(strict_types=1);

/*
 * Copyright (C) 2023 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace Wrm\Events\Updates\UserAuthentication;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * An backend user used by Update Wizards.
 *
 * That way they can use Data Handler no matter how they are executed, e.g. cli, or install tool.
 * That way edits also always have this user assigned.
 *
 * This was mostly copied from TYPO3 core CommandLineUserAuthentication.
 */
class User extends BackendUserAuthentication
{
    /**
     * @var string
     */
    protected $username = '_events_';

    public function __construct()
    {
        if (!$this->isUserAllowedToLogin()) {
            throw new \RuntimeException('Login Error: TYPO3 is in maintenance mode at the moment. Only administrators are allowed access.', 1483971855);
        }
        $this->dontSetCookie = true;
        parent::__construct();
    }

    public function start(ServerRequestInterface $request = null)
    {
        // do nothing
    }

    public function checkAuthentication(ServerRequestInterface $request = null)
    {
        // do nothing
    }

    public function authenticate()
    {
        // check if a _events_ user exists, if not, create one
        $this->setBeUserByName($this->username);
        if (!$this->user['uid']) {
            // create a new BE user in the database
            if (!$this->checkIfEventUserExists()) {
                $this->createEventUser();
            } else {
                throw new \RuntimeException('No backend user named "_events_" could be authenticated, maybe this user is "hidden"?', 1484050401);
            }
            $this->setBeUserByName($this->username);
        }
        if (!$this->user['uid']) {
            throw new \RuntimeException('No backend user named "_events_" could be created.', 1476107195);
        }
        // The groups are fetched and ready for permission checking in this initialization.
        $this->fetchGroupData();
        $this->backendSetUC();
        // activate this functionality for DataHandler
        $this->uc['recursiveDelete'] = true;
    }

    public function backendCheckLogin($proceedIfNoUserIsLoggedIn = false)
    {
        $this->authenticate();
    }

    /**
     * Determines whether a CLI backend user is allowed to access TYPO3.
     * Only when adminOnly is off (=0), and only allowed for admins and CLI users (=2)
     */
    public function isUserAllowedToLogin()
    {
        return in_array((int)$GLOBALS['TYPO3_CONF_VARS']['BE']['adminOnly'], [0, 2], true);
    }

    protected function checkIfEventUserExists(): bool
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $count = $queryBuilder
            ->count('*')
            ->from('be_users')
            ->where($queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter('_events_')))
            ->execute()
            ->fetchColumn(0);
        return (bool)$count;
    }

    protected function createEventUser(): void
    {
        $userFields = [
            'username' => $this->username,
            'password' => $this->generateHashedPassword(),
            'admin'    => 1,
            'tstamp'   => $GLOBALS['EXEC_TIME'],
            'crdate'   => $GLOBALS['EXEC_TIME'],
        ];

        $databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_users');
        $databaseConnection->insert('be_users', $userFields);
    }

    protected function generateHashedPassword(): string
    {
        $cryptoService = GeneralUtility::makeInstance(Random::class);
        $password = $cryptoService->generateRandomBytes(20);
        $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('BE');
        return $hashInstance->getHashedPassword($password);
    }
}
