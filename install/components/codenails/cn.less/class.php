<?php
/**
 * LESS Компилятор для Bitrix
 *
 * @author  Павел Белоусов
 * @license MIT
 *
 */

use Bitrix\Main;
use Bitrix\Main\Application as App;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Page\Asset as Asset;
use Bitrix\Main\SystemException as SystemException;
use Cn\Less\lessCompiler;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

/**
 * Class CnLessComponent
 */
class CnLessComponent extends CBitrixComponent {

	/**
	 * @throws SystemException
	 */
	protected function checkModules() {
		if (!Loader::includeModule('cn.less')) {
			throw new SystemException(Loc::getMessage('CVP_CN_LESS_MODULE_NOT_INSTALLED'));
		}

	}

	/**
	 * @param array $array
	 *
	 * @return boolean
	 */
	protected function checkPermission($array) {

		if (CSite::InGroup($array)) {
			return true;
		}

		return false;

	}

	/**
	 * Load language file
	 */
	public function onIncludeComponentLang() {
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function onPrepareComponentParams($params) {

		$params['COMPRESS']   = ($params['COMPRESS'] == 'Y');
		$params['SOURCE_MAP'] = ($params['SOURCE_MAP'] == 'Y');

		$params['ACCESS_GROUPS'] = is_array($params['ACCESS_GROUPS']) ? $params['ACCESS_GROUPS'] : array('1');

		$params['FILES'] = is_array($params['FILES']) ? $params['FILES'] : array('template_styles.less');

		$params['PATH_TO_FILES'] = isset($params['PATH']) && strlen(trim($params['PATH']))
			? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH']))
			: SITE_TEMPLATE_PATH . '/less/';

		$params['PATH_TO_FILES_CSS'] = isset($params['PATH_CSS']) && strlen(trim($params['PATH_CSS']))
			? preg_replace(array('~^/~', '~/$~'), '/', trim($params['PATH_CSS']))
			: SITE_TEMPLATE_PATH . '/';

		return $params;
	}

	/**
	 *
	 */
	public function executeComponent() {
		if ($this->checkPermission($this->arParams['ACCESS_GROUPS'])) {
			try {

				$this->checkModules();

				$context = App::getInstance()->getContext();

				$rootFolder = $context->getServer()->getDocumentRoot();

				$lessFolder = $this->arParams['PATH_TO_FILES'];
				$fileNames  = $this->arParams['FILES'];
				$compress   = $this->arParams['COMPRESS'];
				$sourceMap  = $this->arParams['SOURCE_MAP'];
				$cssFolder  = $this->arParams['PATH_TO_FILES_CSS'];

				$compile = new lessCompiler($rootFolder, $lessFolder, $fileNames, $cssFolder, $compress, $sourceMap);

				$file = $compile->compile();

				if ($file['error']) {
					// Заменим виндовые слеши.
					$rootFolder = str_replace('\\', '/', $rootFolder);

					/**
					 * @todo Оптимизировать надо бы по хорошему этот момент, некрасиво, когда нельзя управлять стилями, но это в принципе не критичный момент.
					 */
					Asset::getInstance()->addString('<style>.less-error-wrapper{position:fixed;z-index:1500;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.7)}.less-error-content{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:100%}.less-error{padding:40px;color:#666;background:#fff;font:400 17px/30px Consolas,Menlo,"DejaVu Sans Mono","Courier New",monospace,serif;margin:0;word-wrap:break-word;-webkit-box-shadow:0 0 30px rgba(0,0,0,.6);box-shadow:0 0 30px rgba(0,0,0,.6)}.less-error-header{font-size:24px;font-weight:700;margin-bottom:20px;text-align:center;color:#f75b5b}.less-error-content pre{line-height:1;border:0;background-color:transparent}.less-error-hide{display:block;position:absolute;top:20px;right:20px;width:30px;height:30px;background:#f75b5b;color:#fff;font:400 28px/32px Arial,sans-serif;vertical-align:middle;text-align:center;border-radius:15px;cursor:pointer}.less-error-hide:hover{background:#666}</style>');

					// Выведем текст ошибки
					$errorText = '<div id="less-error" class="less-error-wrapper"><div class="less-error-content"><div class="less-error"><div class="less-error-header">' . Loc::GetMessage('CN_ERROR_LESS_COMPILE') . '</div><pre>' . str_replace($rootFolder, '', $file['error']) . '</pre> <span class="less-error-hide" onclick="lessHide(\'less-error\')" title="' . Loc::GetMessage('CN_ERROR_LESS_COMPILE_ERROR_HIDE') . '">&times;</span></div></div></div><script>function lessHide(e){var n=document.getElementById(e);n.style.display="none"}</script>';
					echo $errorText;
				}

			} catch (SystemException $e) {
				ShowError($e->getMessage());
			}
		}
	}

}