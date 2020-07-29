<?php declare(strict_types=1);

namespace StudyPortals\CMS\Page;

use StudyPortals\CMS\Definitions\Hooks\IHookPagePreProcessIncludes;
use StudyPortals\CMS\Definitions\IAssetProvider;
use StudyPortals\Template\NodeNotFoundException;
use StudyPortals\Template\Section;
use StudyPortals\Template\Template;
use StudyPortals\Template\TemplateException;
use StudyPortals\Template\TemplateNodeTree;
use StudyPortals\Utils\ConfigNonExistentException;
use Throwable;

trait PagePresentation
{

    /**
     * Return the fully parsed Template for this Page.
     *
     * <p>This method, together with
     * {@link \StudyPortals\CMS\Page\IPage::_loadTemplate()} does a basic
     * sanity-check on the template's structure using a set of assertions.<br>
     * In a "live" scenario (with assert() disabled), an incorrect template
     * will not yield any errors and will, depending how malformed it is,
     * generate
     * (proper) output.</p>
     * @return Template|string
     * @throws ConfigNonExistentException
     * @throws NodeNotFoundException
     * @throws PageException
     * @throws TemplateException
     */

    public function display()
    {

        $pageTreeNode = $this->getPageTreeNode();
        $Template = Template::create(
            $this->getSite()->template_path .
            $pageTreeNode->getTemplatePath()
        );

        $this->populateCommonFields($Template);

        $pageTree = $this->getPageTree();
        if ($pageTree instanceof IAssetProvider) {
            $this->addAssets($pageTree);
        }

        if ($pageTreeNode instanceof IAssetProvider) {
            $this->addAssets($pageTreeNode);
        }

        // Populate (c.q. display) the panes

        $this->populateTemplate($Template);

        $this->handlePagePreProcessIncludesHook($Template);

        $header = $Template->getChildByName('Header');
        if ($header instanceof Section) {
            $this->processHeaderIncludes($header);
        }
        $footer = $Template->getChildByName('Footer');
        if ($footer instanceof Section) {
            $this->processFooterIncludes($footer);
        }

        return $Template;
    }

    /**
     * Populate common Site fields into a template.
     *
     * <p>This method populates a set of commonly used Site replace variables
     * into the provided template:</p>
     * <ul>
     *        <li>title, subtitle, description</li>
     *        <li>base_url, page_url</li>
     *        <li>framework_path, template_path, module_path, data_path</li>
     *        <li>For each package "module_path_PackageName" is added</li>
     *        <li>For each site "base_url_SiteName" is added</li>
     *        <li>virtual_paths_enabled, virtual_path, virtual_page</li>
     *    </ul>
     *
     * <p>This method is automatically applied to the template returned by
     * Page::display(). As a result of variable scoping in Template4, fields
     * filled through this method are also available to all instances of
     * Template4 included into the Page's main template, with one notable
     * exception:<br>
     * T4Repeater "repeats" do <strong>not</strong> have access to the scope
     * since they are, when used inside Module::displayMain(), parsed before
     * the module's template is appended to the main template. In these cases,
     * modules will need to manually call Page::populateCommonFields() on their
     * template.</p>
     *
     * @param TemplateNodeTree $Template
     *
     * @return void
     * @throws ConfigNonExistentException
     * @throws TemplateException
     * @see Module::Display()
     */

    public function populateCommonFields(TemplateNodeTree $Template): void
    {

        $Template->setValue('title', $this->getPageTreeNode()->getRoute());
        $Template->setValue('subtitle', $this->getSubtitle());
        $Template->setValue('description', $this->getDescription());
        $Template->setValue(
            'structuredMarkupData',
            $this->structuredMarkupData
        );

        $Template->setValue('page_url', $this->getFullURL());
        $Template->setValue('canonical_url', $this->getCanonicalURL());

        $Template->setValue('framework_path', $this->getSite()->framework_path);

        $templateDirectory =
            dirname($this->getPageTreeNode()->getTemplatePath());
        $Template->setValue(
            'template_path',
            $this->getSite()->template_path . $templateDirectory . '/'
        );

        $Template->setValue('debug_mode', defined('DEBUG_MODE') && DEBUG_MODE);

        $Template->setValue('virtual_path', $this->getPageTreeNode()->getURL());
        $Template->setValue('virtual_id', $this->getVirtualId());

        $Template->setValue('base_url', $this->getSite()->getBaseUrl());
    }

    public function addAssets(IAssetProvider $assets): void
    {
        foreach ($assets->getFooterJS() as $file) {
            $this->addFooterInclude(
                $file,
                Includes::JAVASCRIPT
            );
        }
        foreach ($assets->getHeaderJS() as $file) {
            $this->addHeaderInclude(
                $file,
                Includes::JAVASCRIPT
            );
        }
        foreach ($assets->getHeaderCSS() as $file) {
            $file = str_replace('.scss', '.css', $file);

            $this->addHeaderInclude(
                $file,
                Includes::CSS
            );
        }
    }

    private function handlePagePreProcessIncludesHook(Template $template): void
    {
        foreach ($this->getHooks() as $hook) {
            if ($hook instanceof IHookPagePreProcessIncludes) {
                try {
                    $hook->handlePagePreProcessIncludes(
                        $this->getPage(),
                        $template
                    );
                } catch (Throwable $throwable) {
                    try {
                        $hook->handleError($throwable);
                    } catch (Throwable $ignored) {
                        continue;
                    }
                }
            }
        }
    }

    public function getTarget(TemplateNodeTree $template): TemplateNodeTree
    {
        return $template->getChildByName('Body');
    }
}
