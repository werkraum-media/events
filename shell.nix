{
  pkgs ? import <nixpkgs> { }
  ,phps ? import <phps>
}:

let
  php = phps.packages.x86_64-linux.php82.buildEnv {
    extensions = { enabled, all }: enabled ++ (with all; [
      xdebug
    ]);
    extraConfig = ''
      xdebug.mode = debug
      memory_limit = 4G
    '';
  };
  inherit(php.packages) composer;

  projectInstall = pkgs.writeShellApplication {
    name = "project-install";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      rm -rf vendor/ composer.lock .Build/
      composer update --prefer-dist --no-progress --working-dir="$PROJECT_ROOT"
    '';
  };

  projectValidateComposer = pkgs.writeShellApplication {
    name = "project-validate-composer";
    runtimeInputs = [
      php
      composer
    ];
    text = ''
      composer validate
    '';
  };

  projectValidateXml = pkgs.writeShellApplication {
    name = "project-validate-xml";
    runtimeInputs = [
      pkgs.libxml2
      pkgs.wget
      projectInstall
    ];
    text = ''
      project-install
      xmllint --schema vendor/phpunit/phpunit/phpunit.xsd --noout phpunit.xml.dist
      wget --no-check-certificate https://docs.oasis-open.org/xliff/v1.2/os/xliff-core-1.2-strict.xsd --output-document=xliff-core-1.2-strict.xsd
      # shellcheck disable=SC2046
      xmllint --schema xliff-core-1.2-strict.xsd --noout $(find Resources -name '*.xlf')
    '';
  };

  projectPhpstan = pkgs.writeShellApplication {
    name = "project-phpstan";

    runtimeInputs = [
      php
    ];

    text = ''
      ./vendor/bin/phpstan
    '';
  };

  projectCgl = pkgs.writeShellApplication {
    name = "project-cgl";

    runtimeInputs = [
      php
    ];

    text = ''
      PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix --dry-run --diff
    '';
  };

  projectCglFix = pkgs.writeShellApplication {
    name = "project-cgl-fix";

    runtimeInputs = [
      php
    ];

    text = ''
      PHP_CS_FIXER_IGNORE_ENV=1 ./vendor/bin/php-cs-fixer fix
    '';
  };

  projectTestsUnit = pkgs.writeShellApplication {
    name = "project-tests-unit";

    runtimeInputs = [
      php
    ];

    text = ''
      ./vendor/bin/phpunit --testsuite unit --color --testdox
    '';
  };

  projectTestsFunctional = pkgs.writeShellApplication {
    name = "project-tests-functional";

    runtimeInputs = [
      php
    ];

    text = ''
      ./vendor/bin/phpunit --testsuite functional --color --testdox
    '';
  };

in pkgs.mkShell {
  name = "TYPO3 Extension Watchlist";
  buildInputs = [
    projectInstall
    projectValidateComposer
    projectValidateXml
    projectPhpstan
    projectCgl
    projectCglFix
    projectTestsUnit
    projectTestsFunctional
    php
    composer
  ];

  shellHook = ''
    export PROJECT_ROOT="$(pwd)"

    export typo3DatabaseDriver=pdo_sqlite
  '';
}
