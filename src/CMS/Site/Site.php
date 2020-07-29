<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use StudyPortals\CMS\Definitions\Hooks\IHook;
use StudyPortals\CMS\Definitions\IInputHandler;
use StudyPortals\CMS\ModuleCollection\IModuleCollection;
use StudyPortals\CMS\Page\IPage;
use StudyPortals\CMS\PageTree\IPageTree;
use StudyPortals\Template\Template;
use StudyPortals\Utils\HTTP;
use StudyPortals\Utils\IConfig;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Site implements ISite
{
    use SiteCache;

    /**
     * @var int $page_cache_ttl
     */
    public $page_cache_ttl = 3600;
    /**
     * @var string
     */
    public $module_path = './Modules/';
    /**
     * @var string
     */
    public $template_path = './Templates/';
    /**
     * @var IPageTree
     */
    private $PageTree;
    private $Config;
    /**
     * @var array<IHook>
     */
    private $hooks;
    /**
     * @var IInputHandler
     */
    private $inputHandler;
    /**
     * @var string
     */
    private $base_url;

    /**
     * Load a Site from the database.
     *
     * @param IPageTree     $pageTree
     * @param IConfig       $Config
     * @param IInputHandler $inputHandler
     * @param ICacheBuilder $cacheBuilder
     * @param array<IHook>  $hooks
     */

    public function __construct(
        IPageTree $pageTree,
        IConfig $Config,
        IInputHandler $inputHandler,
        ICacheBuilder $cacheBuilder,
        array $hooks = []
    ) {

        $this->hooks = $hooks;
        $this->inputHandler = $inputHandler;

        $this->Config = $Config;

        $this->base_url = self::detectBaseURL($inputHandler);

        $this->PageTree = $pageTree;
        $this->PageTree->setSite($this);

        $this->buildCache($cacheBuilder);

        Template::setDefaultVariable('base_url', $this->getBaseUrl());
    }

    /**
     * @param IInputHandler $inputHandler
     *
     * @return string
     */
    public static function detectBaseURL(IInputHandler $inputHandler): string
    {
        $protocol = 'https://';

        $path = './';
        if ($inputHandler->has(INPUT_SERVER, 'SCRIPT_NAME')) {
            $path =
                trim(dirname($inputHandler->get(
                    INPUT_SERVER,
                    'SCRIPT_NAME'
                )), '/\\') .
                '/';
        }

        $url =
            HTTP::normaliseURL(
                $protocol . $inputHandler->get(INPUT_SERVER, 'HTTP_HOST') . '/' . $path
            );

        if ($url === '') {
            throw new SiteException(
                'Failed to detect a proper base URL for the current Site'
            );
        }

        return $url;
    }

    public function getBaseUrl(): string
    {
        return $this->base_url;
    }

    public function getPageTree(): IPageTree
    {
        return $this->PageTree;
    }

    public function getConfig(): IConfig
    {
        return $this->Config;
    }

    /**
     * @return array<IHook>
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    public function getInputHandler(): IInputHandler
    {
        return $this->inputHandler;
    }
    /**
     * @return IPage<IModuleCollection>
     */
    public function getPage(): IPage
    {
        return $this->getPageTree()->getPage();
    }

    public function getName(): string
    {
        return $this->getPageTree()->getRoute();
    }
}
