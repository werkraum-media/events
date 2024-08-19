<?php

declare(strict_types=1);

namespace WerkraumMedia\Events\Service;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

final class ExtbaseConfigurationManagerService
{
    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager
    ) {
    }

    /**
     * The mapper uses queries, which rely an on the configuration manager.
     * But import is without request, so we ensure it is properly initialized.
     *
     * This should vanish, see: Documentation/Maintenance.rst
     */
    public function configureForBackend(): void
    {
        // TODO: typo3/cms-core:14.0 Remove condition as this method is provided since 13.
        if (method_exists($this->configurationManager, 'setRequest') === false) {
            return;
        }

        $request = new ServerRequest();
        $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $this->configurationManager->setRequest($request);
    }

    public function getInstanceWithBackendContext(): ConfigurationManagerInterface
    {
        $this->configureForBackend();
        return $this->configurationManager;
    }
}
