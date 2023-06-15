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

namespace Wrm\Events\Tests\Functional;

use Codappix\Typo3PhpDatasets\TestingFramework;
use DateTimeImmutable;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\Internal\TypoScriptInstruction;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Wrm\Events\Command\ImportDestinationDataViaConfigruationCommand;
use Wrm\Events\Testing\TypoScriptInstructionModifier;
use Wrm\Events\Tests\ClientFactory;

abstract class AbstractFunctionalTestCase extends FunctionalTestCase
{
    use TestingFramework;

    /**
     * The folder path in file system used to store the imported images.
     *
     * @var string
     */
    protected $fileImportPath = '';

    protected function setUp(): void
    {
        $this->coreExtensionsToLoad = array_merge($this->coreExtensionsToLoad, [
            'filelist',
            'fluid_styled_content',
        ]);

        $this->testExtensionsToLoad = array_merge($this->testExtensionsToLoad, [
            'typo3conf/ext/events',
        ]);

        $this->pathsToProvideInTestInstance = array_merge($this->pathsToProvideInTestInstance, [
            'typo3conf/ext/events/Tests/Functional/Frontend/Fixtures/Sites/' => 'typo3conf/sites',
        ]);

        ArrayUtility::mergeRecursiveWithOverrule($this->configurationToUseInTestInstance, [
            'GFX' => [
                'processor_enabled' => true,
                'processor_path' => '/usr/bin/',
                'processor_path_lzw' => '/usr/bin/',
                'processor' => 'ImageMagick',
            ],
            'SC_OPTIONS' => [
                'Core/TypoScript/TemplateService' => [
                    'runThroughTemplatesPostProcessing' => [
                        'FunctionalTest' => TypoScriptInstructionModifier::class . '->apply',
                    ],
                ],
            ],
        ]);

        parent::setUp();

        $this->setUpBackendUserFromFixture(1);

        $languageServiceFactory = $this->getContainer()->get(LanguageServiceFactory::class);
        if (!$languageServiceFactory instanceof LanguageServiceFactory) {
            throw new \UnexpectedValueException('Did not retrieve LanguageServiceFactory.', 1637847250);
        }
        $GLOBALS['LANG'] = $languageServiceFactory->create('default');

        $fileImportPathConfiguration = 'staedte/beispielstadt/events/';
        $this->fileImportPath = $this->getInstancePath() . '/fileadmin/' . $fileImportPathConfiguration;
        GeneralUtility::mkdir_deep($this->fileImportPath);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG']);
        GeneralUtility::rmdir($this->fileImportPath, true);

        parent::tearDown();
    }

    protected function getTypoScriptInstruction(): TypoScriptInstruction
    {
        return new TypoScriptInstruction(TemplateService::class);
    }

    protected function setUpConfiguration(
        array $destinationDataSettings,
        array $importSettings = []
    ): void {
        $this->setUpFrontendRootPage(1, [], [
            'config' => implode(PHP_EOL, [
                'module.tx_events_pi1.settings.destinationData {',
                implode(PHP_EOL, $destinationDataSettings),
                '}',
                'module.tx_events_import.settings {',
                implode(PHP_EOL, $importSettings),
                '}',
            ]),
        ]);
    }

    protected function &setUpResponses(array $responses): array
    {
        $requests = [];

        $client = ClientFactory::createClientWithHistory($responses, $requests);
        $container = $this->getContainer();
        if ($container instanceof Container) {
            $container->set(ClientInterface::class, $client);
            // For TYPO3 10 support
            $container->set(GuzzleClientInterface::class, $client);
        }

        return $requests;
    }

    protected function executeCommand(
        array $argumentsAndOptions = ['configurationUid' => '1'],
        string $command = ImportDestinationDataViaConfigruationCommand::class
    ): CommandTester {
        $subject = $this->getContainer()->get($command);
        self::assertInstanceOf(Command::class, $subject);

        $tester = new CommandTester($subject);
        $tester->execute(
            $argumentsAndOptions,
            [
                'capture_stderr_separately' => true,
            ]
        );

        return $tester;
    }

    protected function setUpFrontendRendering(): void
    {
        $this->setUpFrontendRootPage(1, $this->getTypoScriptFiles());
    }

    protected function getTypoScriptFiles(): array
    {
        return [
            'constants' => [
                'EXT:events/Configuration/TypoScript/constants.typoscript',
            ],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Configuration/TypoScript/setup.typoscript',
                'EXT:events/Tests/Functional/Frontend/Fixtures/TypoScript/Rendering.typoscript',
            ],
        ];
    }

    /**
     * @api Actual tests can use this method to define the actual date of "now".
     */
    protected function setDateAspect(DateTimeImmutable $dateTime): void
    {
        $context = $this->getContainer()->get(Context::class);
        if (!$context instanceof Context) {
            throw new \TypeError('Retrieved context was of unexpected type.', 1638182021);
        }

        $aspect = new DateTimeAspect($dateTime);
        $context->setAspect('date', $aspect);
    }
}
