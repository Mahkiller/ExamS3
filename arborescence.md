Structure du dossier
Le num�ro de s�rie du volume est 1432-2D15
C:.
�   .gitignore
�   .runway-config.json
�   .windsurfrules
�   composer.json
�   composer.lock
�   docker-compose.yml
�   Examen S3 Design $� D�cembre 2025.pdf
�   index-simple.php
�   LICENSE
�   README.md
�   runway
�   Vagrantfile
�   
+---.cursor
�   +---rules
�           project-overview.mdc
�           
+---.github
�       copilot-instructions.md
�       
+---app
�   +---cache
�   +---commands
�   �       SampleDatabaseCommand.php
�   �       
�   +---config
�   �       bootstrap.php
�   �       config.php
�   �       config_sample.php
�   �       routes.php
�   �       services.php
�   �       
�   +---controllers
�   �       ApiExampleController.php
�   �       
�   +---log
�   +---middlewares
�   �       SecurityHeadersMiddleware.php
�   �       
�   +---models
�   +---utils
�   +---views
�           welcome.php
�           
+---base
�       base.sql
�       
+---public
�       .htaccess
�       index.php
�       
+---vendor
    �   autoload.php
    �   
    +---adhocore
    �   +---cli
    �       �   CHANGELOG.md
    �       �   composer.json
    �       �   LICENSE
    �       �   README.md
    �       �   VERSION
    �       �   
    �       +---src
    �           �   Application.php
    �           �   Exception.php
    �           �   functions.php
    �           �   
    �           +---Exception
    �           �       InvalidArgumentException.php
    �           �       InvalidParameterException.php
    �           �       RuntimeException.php
    �           �       
    �           +---Helper
    �           �       InflectsString.php
    �           �       Normalizer.php
    �           �       OutputHelper.php
    �           �       Shell.php
    �           �       Terminal.php
    �           �       
    �           +---Input
    �           �       Argument.php
    �           �       Command.php
    �           �       Groupable.php
    �           �       Option.php
    �           �       Parameter.php
    �           �       Parser.php
    �           �       Reader.php
    �           �       
    �           +---IO
    �           �       Interactor.php
    �           �       
    �           +---Output
    �                   Color.php
    �                   Cursor.php
    �                   ProgressBar.php
    �                   Table.php
    �                   Writer.php
    �                   
    +---bin
    �       runway
    �       
    +---composer
    �       autoload_classmap.php
    �       autoload_files.php
    �       autoload_namespaces.php
    �       autoload_psr4.php
    �       autoload_real.php
    �       autoload_static.php
    �       ClassLoader.php
    �       installed.json
    �       installed.php
    �       InstalledVersions.php
    �       LICENSE
    �       platform_check.php
    �       
    +---flightphp
    �   +---core
    �   �   �   composer.json
    �   �   �   CONTRIBUTING.md
    �   �   �   index.php
    �   �   �   LICENSE
    �   �   �   phpcs.xml.dist
    �   �   �   phpunit-watcher.yml
    �   �   �   README.md
    �   �   �   
    �   �   +---flight
    �   �       �   autoload.php
    �   �       �   Engine.php
    �   �       �   Flight.php
    �   �       �   
    �   �       +---commands
    �   �       �       AiGenerateInstructionsCommand.php
    �   �       �       AiInitCommand.php
    �   �       �       ControllerCommand.php
    �   �       �       RouteCommand.php
    �   �       �       
    �   �       +---core
    �   �       �       Dispatcher.php
    �   �       �       EventDispatcher.php
    �   �       �       Loader.php
    �   �       �       
    �   �       +---database
    �   �       �       PdoWrapper.php
    �   �       �       
    �   �       +---net
    �   �       �       Request.php
    �   �       �       Response.php
    �   �       �       Route.php
    �   �       �       Router.php
    �   �       �       UploadedFile.php
    �   �       �       
    �   �       +---template
    �   �       �       View.php
    �   �       �       
    �   �       +---util
    �   �               Collection.php
    �   �               Json.php
    �   �               ReturnTypeWillChange.php
    �   �               
    �   +---runway
    �   �   �   .gitignore
    �   �   �   composer.json
    �   �   �   LICENSE
    �   �   �   phpcs.xml
    �   �   �   phpstan.neon
    �   �   �   phpunit.xml
    �   �   �   README.md
    �   �   �   runway
    �   �   �   
    �   �   +---.vscode
    �   �   �       settings.json
    �   �   �       
    �   �   +---scripts
    �   �   �       setup.php
    �   �   �       
    �   �   +---src
    �   �       +---commands
    �   �               AbstractBaseCommand.php
    �   �               
    �   +---tracy-extensions
    �       �   .gitignore
    �       �   composer.json
    �       �   composer.lock
    �       �   flight-db.png
    �       �   flight-request.png
    �       �   flight-tracy-bar.png
    �       �   flight-var-data.png
    �       �   README.md
    �       �   
    �       +---src
    �       �   +---debug
    �       �       +---database
    �       �       �       PdoQueryCapture.php
    �       �       �       PdoQueryCaptureStatement.php
    �       �       �       
    �       �       +---tracy
    �       �               DatabaseExtension.php
    �       �               ExtensionBase.php
    �       �               FlightPanelExtension.php
    �       �               RequestExtension.php
    �       �               ResponseExtension.php
    �       �               SessionExtension.php
    �       �               TracyExtensionLoader.php
    �       �               
    �       +---tests
    �               index.php
    �               
    +---nette
    �   +---php-generator
    �   �   �   composer.json
    �   �   �   license.md
    �   �   �   readme.md
    �   �   �   
    �   �   +---src
    �   �       +---PhpGenerator
    �   �           �   Attribute.php
    �   �           �   ClassLike.php
    �   �           �   ClassManipulator.php
    �   �           �   ClassType.php
    �   �           �   Closure.php
    �   �           �   Constant.php
    �   �           �   Dumper.php
    �   �           �   EnumCase.php
    �   �           �   EnumType.php
    �   �           �   Extractor.php
    �   �           �   Factory.php
    �   �           �   GlobalFunction.php
    �   �           �   Helpers.php
    �   �           �   InterfaceType.php
    �   �           �   Literal.php
    �   �           �   Method.php
    �   �           �   Parameter.php
    �   �           �   PhpFile.php
    �   �           �   PhpLiteral.php
    �   �           �   PhpNamespace.php
    �   �           �   Printer.php
    �   �           �   PromotedParameter.php
    �   �           �   Property.php
    �   �           �   PropertyAccessMode.php
    �   �           �   PropertyHook.php
    �   �           �   PropertyHookType.php
    �   �           �   PsrPrinter.php
    �   �           �   TraitType.php
    �   �           �   TraitUse.php
    �   �           �   Type.php
    �   �           �   Visibility.php
    �   �           �   
    �   �           +---Traits
    �   �                   AttributeAware.php
    �   �                   CommentAware.php
    �   �                   ConstantsAware.php
    �   �                   FunctionLike.php
    �   �                   MethodsAware.php
    �   �                   NameAware.php
    �   �                   PropertiesAware.php
    �   �                   PropertyLike.php
    �   �                   TraitsAware.php
    �   �                   VisibilityAware.php
    �   �                   
    �   +---utils
    �       �   .phpstorm.meta.php
    �       �   composer.json
    �       �   license.md
    �       �   readme.md
    �       �   
    �       �   +---src
    �           �   compatibility.php
    �           �   exceptions.php
    �           �   HtmlStringable.php
    �           �   SmartObject.php
    �           �   StaticClass.php
    �           �   Translator.php
    �           �   
    �           +---Iterators
    �           �       CachingIterator.php
    �           �       Mapper.php
    �           �       
    �           +---Utils
    �                   ArrayHash.php
    �                   ArrayList.php
    �                   Arrays.php
    �                   Callback.php
    �                   DateTime.php
    �                   exceptions.php
    �                   FileInfo.php
    �                   FileSystem.php
    �                   Finder.php
    �                   Floats.php
    �                   Helpers.php
    �                   Html.php
    �                   Image.php
    �                   ImageColor.php
    �                   ImageType.php
    �                   Iterables.php
    �                   Json.php
    �                   ObjectHelpers.php
    �                   Paginator.php
    �                   Random.php
    �                   Reflection.php
    �                   ReflectionMethod.php
    �                   Strings.php
    �                   Type.php
    �                   Validators.php
    �                   
    +---tracy
        +---tracy
            �   .phpstorm.meta.php
            �   composer.json
            �   eslint.config.js
            �   license.md
            �   package.json
            �   readme.md
            �   
            +---examples
            �   �   ajax-fetch.php
            �   �   ajax-jquery.php
            �   �   barDump.php
            �   �   dump-snapshot.php
            �   �   dump.php
            �   �   exception.php
            �   �   fatal-error.php
            �   �   notice.php
            �   �   output-debugger.php
            �   �   preloading.php
            �   �   redirect.php
            �   �   warning.php
            �   �   
            �   +---assets
            �   �       arrow.png
            �   �       E_COMPILE_ERROR.php
            �   �       style.css
            �   �       
            �   +---log
            +---src
            �   �   tracy.php
            �   �   
            �   +---Bridges
            �   �   +---Nette
            �   �   �       Bridge.php
            �   �   �       MailSender.php
            �   �   �       TracyExtension.php
            �   �   �       
            �   �   +---Psr
            �   �           PsrToTracyLoggerAdapter.php
            �   �           TracyToPsrLoggerAdapter.php
            �   �           
            �   +---Tracy
            �       �   functions.php
            �       �   Helpers.php
            �       �   
            �       +---assets
            �       �       helpers.js
            �       �       reset.css
            �       �       table-sort.css
            �       �       table-sort.js
            �       �       tabs.css
            �       �       tabs.js
            �       �       toggle.css
            �       �       toggle.js
            �       �       
            �       +---Bar
            �       �   �   Bar.php
            �       �   �   DefaultBarPanel.php
            �       �   �   IBarPanel.php
            �       �   �   
            �       �   +---assets
            �       �   �       bar.css
            �       �   �       bar.js
            �       �   �       bar.phtml
            �       �   �       loader.phtml
            �       �   �       panels.phtml
            �       �   �       
            �       �   +---panels
            �       �           dumps.panel.phtml
            �       �           dumps.tab.phtml
            �       �           info.panel.phtml
            �       �           info.tab.phtml
            �       �           warnings.panel.phtml
            �       �           warnings.tab.phtml
            �       �           
            �       +---BlueScreen
            �       �   �   BlueScreen.php
            �       �   �   CodeHighlighter.php
            �       �   �   
            �       �   +---assets
            �       �           bluescreen.css
            �       �           bluescreen.js
            �       �           content.phtml
            �       �           page.phtml
            �       �           section-cli.phtml
            �       �           section-environment.phtml
            �       �           section-exception-causedBy.phtml
            �       �           section-exception-exception.phtml
            �       �           section-exception.phtml
            �       �           section-header.phtml
            �       �           section-http.phtml
            �       �           section-lastMutedError.phtml
            �       �           section-stack-callStack.phtml
            �       �           section-stack-exception.phtml
            �       �           section-stack-fiber.phtml
            �       �           section-stack-generator.phtml
            �       �           section-stack-sourceFile.phtml
            �       �           
            �       +---Debugger
            �       �   �   Debugger.php
            �       �   �   DeferredContent.php
            �       �   �   DevelopmentStrategy.php
            �       �   �   ProductionStrategy.php
            �       �   �   
            �       �   +---assets
            �       �           error.500.phtml
            �       �           
            �       +---Dumper
            �       �   �   Describer.php
            �       �   �   Dumper.php
            �       �   �   Exposer.php
            �       �   �   Renderer.php
            �       �   �   Value.php
            �       �   �   
            �       �   +---assets
            �       �           dumper-dark.css
            �       �           dumper-light.css
            �       �           dumper.js
            �       �           
            �       +---Logger
            �       �       ILogger.php
            �       �       Logger.php
            �       �       
            �       +---OutputDebugger
            �       �       OutputDebugger.php
            �       �       
            �       +---Session
            �               FileSession.php
            �               NativeSession.php
            �               SessionStorage.php
            �               
            +---tools
                +---create-phar
                �       create-phar.php
                �       
                +---open-in-editor
                    +---linux
                    �       install.sh
                    �       open-editor.sh
                    �       
                    +---windows
                            install.cmd
                            open-editor.js