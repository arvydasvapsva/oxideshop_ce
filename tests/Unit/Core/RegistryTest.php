<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxConfig;
use OxidEsales\EshopCommunity\Core\Registry;
use oxLang;
use oxRegistry;
use oxSession;
use oxStr;
use oxUtils;
use stdClass;

/**
 * Test case for \OxidEsales\Eshop\Core\Registry
 */
class RegistryTest extends \OxidTestCase
{

    /**
     * Test, that the method get creates the object of the correct current edition namespace.
     */
    public function testEditionSpecificObjectIsCreatedCorrect()
    {
        $utilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();

        $edition = $this->getConfig()->getEdition();
        $expectedClass = 'OxidEsales\EshopCommunity\Core\UtilsObject';

        switch ($edition) {
            case 'CE':
                $expectedClass = 'OxidEsales\EshopCommunity\Core\UtilsObject';
                break;
            case 'PE':
                $expectedClass = 'OxidEsales\EshopProfessional\Core\UtilsObject';
                break;
            case 'EE':
                $expectedClass = 'OxidEsales\EshopEnterprise\Core\UtilsObject';
                break;
        }

        $this->assertEquals($expectedClass, get_class($utilsObject));
    }

    /**
     * test for OxReg::get()
     */
    public function testGet()
    {
        $oStr = Registry::get("oxstr");
        $this->assertTrue($oStr instanceof \OxidEsales\EshopCommunity\Core\Str);
    }

    /**
     * Tests that Registry is functioning in non case sensitive way
     */
    public function testSetGetCaseInsensitive()
    {
        $oStr = Registry::get("oxSTR");
        $oStr->test = "testValue";
        //differen case
        $oStr2 = Registry::get("OxStr");
        $this->assertEquals("testValue", $oStr2->test);
    }

    /**
     * tests OxReg::get() if the same instance is given every time
     */
    public function testGetSameInstance()
    {
        $oStr = Registry::get("oxstr");
        $oStr->test = "testValue";
        $oStr = Registry::get("oxstr");
        $this->assertEquals("testValue", $oStr->test);
    }

    /**
     * Tests OxReg::get() and OxReg::set()
     */
    public function testSetGetInstance()
    {
        $oTest = new stdClass();
        $oTest->testPublic = "testPublicVal";

        Registry::set("testCase", $oTest);
        $oTest2 = Registry::get("testCase");

        $this->assertEquals("testPublicVal", $oTest2->testPublic);
        Registry::set("testCase", null);
    }

    /**
     * Test for OxReg::getConfig()
     */
    public function testGetConfig()
    {
        $oSubj = $this->getConfig();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Config);
    }

    public function testGetSession()
    {
        $oSubj = Registry::getSession();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Session);
    }

    public function testGetLang()
    {
        $oSubj = Registry::getLang();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Language);
    }

    public function testGetLUtils()
    {
        $oSubj = Registry::getUtils();
        $this->assertTrue($oSubj instanceof \OxidEsales\EshopCommunity\Core\Utils);
    }

    public function testGetKeys()
    {
        Registry::set("testKey", "testVal");
        $this->assertTrue(in_array("testKey", Registry::getKeys()));
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
    }

    public function testUnset()
    {
        \OxidEsales\Eshop\Core\Registry::set("testKey", "testVal");
        $this->assertTrue(in_array("testKey", Registry::getKeys()));
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
        $this->assertFalse(in_array("testKey", Registry::getKeys()));
    }

    public function testInstanceExists()
    {
        \OxidEsales\Eshop\Core\Registry::set("testKey", "testVal");
        $this->assertTrue(Registry::instanceExists('testKey'));
        \OxidEsales\Eshop\Core\Registry::set("testKey", null);
        $this->assertFalse(Registry::instanceExists('testKey'));
    }

    /**
     * Test getter for ControllerClassNameProvider.
     */
    public function testGetControllerClassNameResolver()
    {
        $object = Registry::getControllerClassNameResolver();
        $this->assertTrue(is_a($object, '\OxidEsales\EshopCommunity\Core\Contract\ClassNameResolverInterface'));
        $this->assertTrue(is_a($object, '\OxidEsales\EshopCommunity\Core\Routing\ControllerClassNameResolver'));
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForBcUtilsObjectClassName()
    {
        $object = Registry::get('oxUtilsObject');
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Test Registry::get() for UtilsObject.
     * NOTE: unit tests always get a brand new instance of UtilsObject.
     */
    public function testRegistryGetForNamespaceUtilsObject()
    {
        $object = Registry::get(\OxidEsales\Eshop\Core\UtilsObject::class);
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Test Registry::getUtilsObject().
     */
    public function testRegistryGetUtilsObject()
    {
        $object = Registry::getUtilsObject();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsObject::class));
    }

    /**
     * Verify that Registry::get can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsNamespaces()
    {
        $className = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($className), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::get can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistryGetSupportsBcClasses()
    {
        $className = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        Registry::get($virtualClassName);
        $this->assertTrue(Registry::instanceExists($className));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($className), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::set can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespacesBc()
    {
        $bcClassName = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $object = oxNew($bcClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($virtualClassName));
    }

    /**
     * Verify that Registry::set can be called with virtualClassname as well as bc class name to get the same object.
     */
    public function testRegistrySetSupportsNamespaces()
    {
        $bcClassName = 'oxbasket';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Basket::class;
        $object = oxNew($virtualClassName);
        Registry::set($bcClassName, $object);
        $this->assertTrue(Registry::instanceExists($bcClassName));
        $this->assertTrue(Registry::instanceExists($virtualClassName));
        $this->assertSame(Registry::get($bcClassName), Registry::get($virtualClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyBcClass()
    {
        $bcClassName = 'oxArticle';
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertEquals($virtualClassName, Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyConfigFile()
    {
        $bcClassName = 'oxConfigFile';
        $virtualClassName = \OxidEsales\Eshop\Core\ConfigFile::class;
        $this->assertEquals($virtualClassName, Registry::getStorageKey($bcClassName));
    }

    /**
     * Test Registry::getStorageKey.
     */
    public function testGetStorageKeyNamespaceClass()
    {
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $this->assertEquals($virtualClassName, Registry::getStorageKey($virtualClassName));
    }

    /**
     * Have a look at the registry keys.
     */
    public function testRegistryKeys()
    {
        $storageKeys = Registry::getKeys();
        $this->assertTrue(in_array('OxidEsales\Eshop\Core\UtilsObject', $storageKeys));
        $this->assertTrue(in_array('OxidEsales\Eshop\Core\ConfigFile', $storageKeys));
    }

    /**
     * IMPORTANT: When you explicitly set/get edition classes, the edition namespace is
     *            used as storage key and not the virtual namespace class name.
     *            This is no problem as we will always use virtual class names but when edition classes are used
     *            be careful as long as the bc layer exists.
     */
    public function testRegistryAndEditionNamespace()
    {
        $className = \OxidEsales\EshopCommunity\Application\Model\Order::class;
        $virtualClassName = \OxidEsales\Eshop\Application\Model\Order::class;
        Registry::get($className);
        $this->assertTrue(Registry::instanceExists($className));
        //When you explicitly request an EDITION namespace object this is NOT stored under the virtual namespace key.
        $this->assertFalse(Registry::instanceExists($virtualClassName));
    }

    /**
     * Test Registry::getInputValidator().
     */
    public function testRegistryGetInputValidator()
    {
        $object = Registry::getInputValidator();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\InputValidator::class));
    }

    /**
     * Test Registry::getPictureHandler().
     */
    public function testRegistryGetPictureHandler()
    {
        $object = Registry::getPictureHandler();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\PictureHandler::class));
    }

    /**
     * Test Registry::getSeoDecoder().
     */
    public function testRegistryGetSeoDecoder()
    {
        $object = Registry::getSeoDecoder();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\SeoDecoder::class));
    }

    /**
     * Test Registry::getSeoEncoder().
     */
    public function testRegistryGetSeoEncoder()
    {
        $object = Registry::getSeoEncoder();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\SeoEncoder::class));
    }

    /**
     * Test Registry::getUtilsCount().
     */
    public function testRegistryGetUtilsCount()
    {
        $object = Registry::getUtilsCount();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsCount::class));
    }

    /**
     * Test Registry::getUtilsDate().
     */
    public function testRegistryGetUtilsDate()
    {
        $object = Registry::getUtilsDate();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsDate::class));
    }

    /**
     * Test Registry::getUtilsFile().
     */
    public function testRegistryGetUtilsFile()
    {
        $object = Registry::getUtilsFile();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsFile::class));
    }

    /**
     * Test Registry::getUtilsPic().
     */
    public function testRegistryGetUtilsPic()
    {
        $object = Registry::getUtilsPic();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsPic::class));
    }

    /**
     * Test Registry::getUtilsServer().
     */
    public function testRegistryGetUtilsServer()
    {
        $object = Registry::getUtilsServer();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsServer::class));
    }

    /**
     * Test Registry::getUtilsString().
     */
    public function testRegistryGetUtilsString()
    {
        $object = Registry::getUtilsString();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsString::class));
    }

    /**
     * Test Registry::getUtilsUrl().
     */
    public function testRegistryGetUtilsUrl()
    {
        $object = Registry::getUtilsUrl();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsUrl::class));
    }

    /**
     * Test Registry::getUtilsView().
     */
    public function testRegistryGetUtilsView()
    {
        $object = Registry::getUtilsView();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsView::class));
    }

    /**
     * Test Registry::getUtilsXml().
     */
    public function testRegistryGetUtilsXml()
    {
        $object = Registry::getUtilsXml();
        $this->assertTrue(is_a($object, \OxidEsales\Eshop\Core\UtilsXml::class));
    }

    /**
     * Test Registry dedicated getters vs. Registry::get() for BC classes.
     * Test belongs to BC layer.
     */
    public function testRegistryBCGet()
    {
        $this->assertTrue(Registry::getInputValidator() === Registry::get('oxInputValidator'));
        $this->assertTrue(Registry::getPictureHandler() === Registry::get('oxPictureHandler'));
        $this->assertTrue(Registry::getSeoDecoder() === Registry::get('oxSeoDecoder'));
        $this->assertTrue(Registry::getSeoEncoder() === Registry::get('oxSeoEncoder'));
        $this->assertTrue(Registry::getUtilsCount() === Registry::get('oxUtilsCount'));
        $this->assertTrue(Registry::getUtilsDate() === Registry::get('oxUtilsDate'));
        $this->assertTrue(Registry::getUtilsFile() === Registry::get('oxUtilsFile'));
        $this->assertTrue(Registry::getUtilsPic() === Registry::get('oxUtilsPic'));
        $this->assertTrue(Registry::getUtilsServer() === Registry::get('oxUtilsServer'));
        $this->assertTrue(Registry::getUtilsString() === Registry::get('oxUtilsString'));
        $this->assertTrue(Registry::getUtilsUrl() === Registry::get('oxUtilsUrl'));
        $this->assertTrue(Registry::getUtilsView() === Registry::get('oxUtilsView'));
        $this->assertTrue(Registry::getUtilsXml() === Registry::get('oxUtilsXml'));
        $this->assertTrue(Registry::getConfig() === Registry::get('oxConfig'));
        $this->assertTrue(Registry::getSession() === Registry::get('oxSession'));
        $this->assertTrue(Registry::getLang() === Registry::get('oxLang'));
        $this->assertTrue(Registry::getUtils() === Registry::get('oxUtils'));
        $this->assertTrue(Registry::getUtilsObject() === Registry::get('oxUtilsObject'));
    }

    /**
     * @dataProvider dataProviderTestRegistryGettersReturnProperInstances
     *
     * @param $method
     * @param $instance
     */
    public function testRegistryGettersReturnProperInstances($method, $instance)
    {
        $object = Registry::$method();

        $this->assertInstanceOf($instance, $object, 'Registry::' . $method . '() returns an object, which is an instance of ' . $instance);
    }

    /**
     * @return array
     */
    public function dataProviderTestRegistryGettersReturnProperInstances()
    {
        return [
            ['getInputValidator', \OxidEsales\Eshop\Core\InputValidator::class],
            ['getPictureHandler', \OxidEsales\Eshop\Core\PictureHandler::class],
            ['getSeoDecoder', \OxidEsales\Eshop\Core\SeoDecoder::class],
            ['getSeoEncoder', \OxidEsales\Eshop\Core\SeoEncoder::class],
            ['getUtilsCount', \OxidEsales\Eshop\Core\UtilsCount::class],
            ['getUtilsDate', \OxidEsales\Eshop\Core\UtilsDate::class],
            ['getUtilsFile', \OxidEsales\Eshop\Core\UtilsFile::class],
            ['getUtilsPic', \OxidEsales\Eshop\Core\UtilsPic::class],
            ['getUtilsServer', \OxidEsales\Eshop\Core\UtilsServer::class],
            ['getUtilsString', \OxidEsales\Eshop\Core\UtilsString::class],
            ['getUtilsUrl', \OxidEsales\Eshop\Core\UtilsUrl::class],
            ['getUtilsView', \OxidEsales\Eshop\Core\UtilsView::class],
            ['getUtilsXml', \OxidEsales\Eshop\Core\UtilsXml::class],
            ['getConfig', \OxidEsales\Eshop\Core\Config::class],
            ['getSession', \OxidEsales\Eshop\Core\Session::class],
            ['getLang', \OxidEsales\Eshop\Core\Language::class],
            ['getUtils', \OxidEsales\Eshop\Core\Utils::class],
            ['getUtilsObject', \OxidEsales\Eshop\Core\UtilsObject::class],
        ];
    }

    /**
     * @dataProvider dataProviderTestRegistryGettersReturnIdenticalObjects
     *
     * @param $method
     */
    public function testRegistryGettersReturnIdenticalObjects($method)
    {
        $object_1 = Registry::$method();
        $object_2 = Registry::$method();

        $this->assertTrue(($object_1 === $object_2), '2 consecutive calls to Registry::' . $method . '() will return identical objects');
    }

    /**
     * @return array
     */
    public function dataProviderTestRegistryGettersReturnIdenticalObjects()
    {
        return [
            ['getInputValidator'],
            ['getPictureHandler'],
            ['getSeoDecoder'],
            ['getSeoEncoder'],
            ['getUtilsCount'],
            ['getUtilsDate'],
            ['getUtilsFile'],
            ['getUtilsPic'],
            ['getUtilsServer'],
            ['getUtilsString'],
            ['getUtilsUrl'],
            ['getUtilsView'],
            ['getUtilsXml'],
            ['getConfig'],
            ['getSession'],
            ['getLang'],
            ['getUtils'],
            ['getUtilsObject'],
        ];
    }
}
