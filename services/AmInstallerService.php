<?php
namespace Craft;

class AmInstallerService extends BaseApplicationComponent
{
    private $installedModules = array();

    /**
     * Get the information of a module.
     *
     * @param string $moduleName
     * @param bool   $getInstallInformation [Optional] Only get the install information of a module.
     *
     * @return array
     */
    public function getModule($moduleName, $getInstallInformation = false)
    {
        // Retrieve the requested module only
        $moduleData = $getInstallInformation ? array() : $this->_getAvailableModules($moduleName);
        // Add the additional installation information
        $this->_getModuleInstallInformation($moduleName, $moduleData);
        return $moduleData;
    }

    /**
     * Get the information of all modules.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->_getAvailableModules();
    }

    /**
     * Get installed modules.
     */
    private function _setInstalledModules()
    {
        $allInstalledModules = AmInstallerRecord::model()->findAll();
        if ($allInstalledModules) {
            foreach ($allInstalledModules as $key => $module) {
                $attributes = $module->getAttributes();
                $this->installedModules[ $attributes['handle'] ] = $attributes['installed'] == '1';
            }
        }
    }

    /**
     * Check whether a module is installed.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    private function _isModuleInstalled($moduleName)
    {
        return isset($this->installedModules[$moduleName]) ? $this->installedModules[$moduleName] : false;
    }

    /**
     * Get all available modules.
     *
     * @param string $getModuleByName Get the module information of a specific module.
     *
     * @return array
     */
    private function _getAvailableModules($getModuleByName = '')
    {
        // Find installed modules
        $this->_setInstalledModules();
        // Set the information of every module
        $availableModules = array(
            'algemeen' => array(
                'name'        => 'Algemeen',
                'description' => 'Veel gebruikte velden toevoegen en standaard pagina\'s zoals contact, zoekresultaat en dergelijke.',
                'installed'   => $this->_isModuleInstalled('algemeen')
            ),
            'diensten' => array(
                'name'        => 'Diensten',
                'description' => 'Dienst overzicht en diensten.',
                'installed'   => $this->_isModuleInstalled('diensten')
            ),
            'medewerkers' => array(
                'name'        => 'Medewerkers',
                'description' => 'Medewerkers overzicht en medewerkers.',
                'installed'   => $this->_isModuleInstalled('medewerkers')
            ),
            'news' => array(
                'name'        => 'Nieuws',
                'description' => 'Nieuws overzicht en nieuwsberichten.',
                'installed'   => $this->_isModuleInstalled('news')
            ),
            'producten' => array(
                'name'        => 'Producten',
                'description' => 'Producten overzicht en producten.',
                'installed'   => $this->_isModuleInstalled('producten')
            ),
            'referenties' => array(
                'name'        => 'Referenties',
                'description' => 'Referenties overzicht en referenties.',
                'installed'   => $this->_isModuleInstalled('referenties')
            ),
            'vacatures' => array(
                'name'        => 'Vacatures',
                'description' => 'Vacatures overzicht en vacatures.',
                'installed'   => $this->_isModuleInstalled('vacatures')
            )
        );
        // Return all or a specific module
        if (! empty($getModuleByName)) {
            return isset($availableModules[$getModuleByName]) ? $availableModules[$getModuleByName] : false;
        }
        return $availableModules;
    }

    /**
     * Add additional information for the module installation page.
     *
     * @param string $moduleName  The module name.
     * @param array  &$moduleData The module data.
     */
    private function _getModuleInstallInformation($moduleName, &$moduleData)
    {
        $files = array(
            'tabs',
            'main',
            'sections',
            'fields',
            'fieldLayout',
            'templateGroup',
            'entries'
        );
        foreach ($files as $file) {
            $fileLocation = craft()->path->getPluginsPath() . 'aminstaller/resources/install/information/' . $moduleName . '/' . $file . '.php';
            if (file_exists($fileLocation)) {
                $fileContent = include($fileLocation);
                $moduleData[$file] = $fileContent;
            }
        }
    }
}