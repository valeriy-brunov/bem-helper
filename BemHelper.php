<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Bem helper
 */
class BemHelper extends Helper
{
    /**
     * Массив хэлперов, которые будут использоваться внутри хэлпера BEM.
     */
    public $helpers = ['Html', 'Form'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
	 * Тип тэга. Это может быть "div", "span", "a" и т.д.
	 *
	 * @var string
	 */
	protected $_tag = null;

    /**
	 * Имя текущего блока.
	 *
	 * @var string
	 */
	protected $_name_current_block = null;

	/**
	 * Имя текущего блока или элемента, с которым в данный момент производиться работа.
	 *
	 * @var string
	 */
	protected $_name = null;

	/**
	 * Полное имя блока или элемента. Данное имя класса должно идти первым перед модификаторами
	 * или миксами при формировании html шаблона.
	 *
	 * @var string
	 */
	protected $_full_name = null;

	/**
	 * Метка для блока или элемента.
	 */
	protected $_id = null;

	/**
	 * Массив, содержащий перечень модификаторов, из элемента cakePHP.
	 *
	 * @var array
	 */
	protected $_mod = [];
	
	/**
	 * Массив, содержащий перечень классов модификаторов.
	 *
	 * @var array
	 */
	protected $_mod_arr = [];

	/**
	 * Строка, содержащая перечень классов модификаторов.
	 *
	 * @var array
	 */
	protected $_mod_str = null;

	/**
	 * Массив, содержащий перечень миксов, из элемента cakePHP.
	 *
	 * @var array
	 */
	protected $_mix = [];
	
	/**
	 * Массив, содержащий перечень классов миксов.
	 *
	 * @var array
	 */
	protected $_mix_arr = [];

	/**
	 * Строка, содержащая перечень классов миксов.
	 *
	 * @var array
	 */
	protected $_mix_str = null;

	/**
	 * Контент (содержимое) блока или элемента.
	 *
	 * @var string
	 */
	protected $_content = '';

	/**
	 * Для добавления или изменения контента (содержимого) из элемента cakePHP.
	 *
	 * @var array
	 */
	protected $_arr_content = [];

	/**
	 * Массив атрибутов для блока или элемента.
	 *
	 * @var array
	 */
	protected $_attr = [];

	/**
	 * Массив атрибутов из элемента cakephp.
	 *
	 * @var array
	 */
	protected $_attr_element_cakephp = [];

	/**
	 * Строка атрибутов для блока или элемента.
	 *
	 * @var string
	 */
	protected $_attr_str = null;

	/**
	 * Разделитель между классами.
	 *
	 * @var string
	 */
	protected $_space = ' ';

	/**
	 * Массив меток для AJAX-запросов.
	 */
	protected $_views = [];

	/**
	 * Содержит AJAX-метку, которую необходимо показать.
	 *
	 * @var string
	 */
	protected $_show = null;

	/**
	 * Содержит вспомогательные переменные.
	 *
	 * @var array
	 */
	protected $_var = [];
	
	/**
	 * Массив тегов для замены текущего тега при необходимости.
	 * 
	 * @var array
	 */
	protected $_arr_tag = [];

	/**
	 * Метка для блока или элемента.
	 *
	 * @param {string|int} $id
	 * 		Значение метки.
	 */
	public function id( $id ): String
	{
		
		return (string)$id;
	}

	/**
	 * Возвращает значение вспомогательной переменной.
	 *
	 * @param {string} $name
	 * 		Имя в спомогательной переменной.
	 * @return {int|string}
	 * 		Значение, содержащейся в спомогательной переменной.
	 */
	public function var(String $name )
	{
		
		return $this->_var[ $name ];
	}
	
	/**
	 * Загружает указанный блок и передает ему, при необходимости, параметры.
	 * Заменяет стандартный `элемент cakePHP`.
	 * 
	 * @param {string} $name
	 * 		Имя блока.
	 * @param {array} $conf
	 * 		Массив настроек блока.
	 * @param {string} $id
	 * 		Метка блока.
	 * @return {string}
	 * 		Html-код блока. 
	 */
	public function block(String $name, array $conf = [], String $id = null ): String
	{

		$object_bem = $this->_config( $name, $conf, $id );

		return $object_bem['conf']["$name"]->getView()->element( 'blocks/' . $name, $object_bem );
	}

	/**
	 * Создание нового экземпляра блока и массива параметров конфигурации для данного экземпляра блока.
	 *
	 * @param {string} $name
	 * 		Имя блока.
	 * @param {array} $conf
	 * 		Массив настроек блока.
	 * @param {string} $id
	 * 		Метка блока.
	 * @return {object}
	 * 		Объект блока.
	 */
	protected function _config(String $name, array $conf = [], String $id = null )
    {

		if ( count($conf) > 0 ) {

			$arr = $conf['conf'];
		}

		$conf['conf']["$name"] = clone ($this);

		if ( isset($id) ) {
	
			$conf['conf']["$name"]->_id = $id;
		}

        $conf['conf']["$name"]->_name_current_block = $name;

        if ( isset($arr) and count($arr) > 0 ) {

			if ( isset($arr['mod']) ) {

				$conf['conf']["$name"]->_mod = $arr['mod'];
			}
			else {

				$conf['conf']["$name"]->_mod = [];
			}

			if ( isset($arr['mix']) ) {

				$conf['conf']["$name"]->_mix = $arr['mix'];
			}
			else {

				$conf['conf']["$name"]->_mix = [];
			}

			if ( isset($arr['content']) ) {

				$conf['conf']["$name"]->_arr_content = $arr['content'];
			}
			else {

				$conf['conf']["$name"]->_arr_content = [];
			}

			if ( isset($arr['attr']) ) {

				$conf['conf']["$name"]->_attr_element_cakephp = $arr['attr'];
			}
			else {

				$conf['conf']["$name"]->_attr_element_cakephp = [];
			}
			
			if ( isset($arr['tag']) ) {

				$conf['conf']["$name"]->_arr_tag = $arr['tag'];
			}
			else {

				$conf['conf']["$name"]->_arr_tag = [];
			}

			if ( isset($arr['views']) ) {

				$conf['conf']["$name"]->_views = $arr['views'];
			}
			else {

				$conf['conf']["$name"]->_views = [];
			}

			if ( isset($arr['show']) ) {

				$conf['conf']["$name"]->_show = $arr['show'];
			}
			else {

				$conf['conf']["$name"]->_show = null;
			}

			if ( isset($arr['var']) ) {

				$conf['conf']["$name"]->_var = $arr['var'];
			}
			else {

				$conf['conf']["$name"]->_var = [];
			}
		}

        return $conf;
    }

	/**
	 * Генератор массива классов модификаторов.
	 */
	protected function _generatorModClass(): void
	{
		
		if ( isset($this->_mod[ "{$this->_name}" ]) ) {

			foreach ( $this->_mod[ "{$this->_name}" ] as $k => $val ) {

				if ( ctype_digit( strval($k) ) and $val[0] == '_' ) {

					$this->_mod_arr[] = $this->_name . $val;
				}
				elseif ( is_array($val) and isset($this->_id) ) {

					if ( preg_match('/' . $k . '/', $this->_id ) ) {

						foreach ( $val as $val2) {

							$this->_mod_arr[] = $this->_name . $val2;
						}
					}
				}
				elseif ( is_string($val) ) {

					$this->_mod_arr[] = $this->_name . $val;
				}
			}
		}
		$this->_mod_arr = array_unique( $this->_mod_arr );
	}

	/**
	 * Генератор массива классов миксов.
	 */
	protected function _generatorMixClass(): void
	{

		if ( isset($this->_mix[ "{$this->_name}" ]) ) {

			$block = '';
			foreach ( $this->_mix[ "{$this->_name}" ] as $k => $val ) {

				if ( ctype_digit( strval($k) ) ) {

					if ( $val[0] == '_' ) {

						$this->_mix_arr[] = $block . $val;
					}
					else {

						$block = $val;
						$this->_mix_arr[] = $val;
					}
				}
				elseif ( is_array($val) and isset($this->_id) ) {

					if ( preg_match('/' . $k . '/', $this->_id ) ) {

						foreach ( $val as $val2) {

							if ( $val2[0] == '_' ) {

								$this->_mix_arr[] = $block . $val2;
							}
							else {

								$block = $val2;
								$this->_mix_arr[] = $val2;
							}
						}
					}
				}
				elseif ( is_string($val) and isset($this->_id) ) {

					if ( preg_match('/' . $k . '/', $this->_id ) ) {

						$this->_mix_arr[] = $val;
					}
				}
			}
		}
		$this->_mix_arr = array_unique( $this->_mix_arr );
	}

	/**
	 * Изменяет контент внутри блока или элемента при необходимости.
	 */
	protected function _generatorContent(): void
	{

		if ( isset($this->_arr_content[ "{$this->_name}" ]) ) {

			if ( is_string($this->_arr_content[ "{$this->_name}" ]) ) {

		 		$this->_content = $this->_arr_content[ "{$this->_name}" ];
		 	}
		 	else {

				$content = [];
				foreach ( $this->_arr_content[ "{$this->_name}" ] as $k => $val ) {

					if ( ctype_digit( strval($k) ) ) {

						$content[] = $val;
					}
					elseif ( is_array($val) and isset($this->_id) ) {

						if ( preg_match('/' . $k . '/', $this->_id ) ) {

							$content[] = implode('', $val);
						}
					}
					else {

						if ( preg_match('/' . $k . '/', $this->_id ) ) {

							$content[] = $val;
						}
					}
				}
				$this->_content = implode( '', $content );
			}
		}
	}

	/**
	 * Из массива атрибутов создаёт массив строк атрибутов.
	 */
	protected function _generatorAttr(): void
	{

		if ( isset($this->_attr_element_cakephp[ "{$this->_name}" ] ) ) {

			foreach ( $this->_attr_element_cakephp[ "{$this->_name}" ] as $k => $val ) {

				if ( is_array($val) and isset($this->_id) ) {

					if ( preg_match('/' . $k . '/', $this->_id ) ) {

						foreach ( array_keys($val) as $val2 ) {

							$this->_attr[] = $val2 . "='" . $val[$val2] . "'";
						}
					}
				}
				elseif ( is_string($val) ) {

					$this->_attr[] = $k . "='" . $val . "'";
				}
			}
		}
	}

	/**
	 * Добавляет класс 'i-bem' при необходимости.
	 */
	protected function _ibem(): void
	{

		foreach ( $this->_attr as $val ) {

			if ( strpos($val, 'data-bem') === 0 ) {

				if ( count($this->_mix_arr) > 0 ) {

					$this->_mix_arr[] = ' i-bem';
					break;
				}
				if ( count($this->_mod_arr) > 0 ) {

					$this->_mod_arr[] = ' i-bem';
					break;
				}
				if ( count($this->_mix_arr) == 0 and count($this->_mod_arr) == 0 ) {

					$this->_mod_arr[] = ' i-bem';
					break;
				}
			}
		}
	}

	/**
	 * Заменяет, при необходимости, тип тега на новый.
	 */
	protected function _tag(): void
	{

		if ( isset( $this->_arr_tag[ "{$this->_name}" ] ) ) {

			if ( is_string( $this->_arr_tag[ "{$this->_name}" ] ) ) {

				$this->_tag = $this->_arr_tag[ "{$this->_name}" ];
			}
			elseif ( is_array( $this->_arr_tag[ "{$this->_name}" ] ) ) {

				foreach ( $this->_arr_tag[ "{$this->_name}" ] as $key => $val ) {

					if ( ctype_digit( strval($key) ) ) {

						$this->_tag = $val;
						break;
					}
					elseif ( preg_match('/' . $key . '/', '3' ) ) {

						$this->_tag = $val;
					}
				}
			}
		}
	}

	/**
	 * Возвращает html код блока или элемента. Будет иметь закрывающий тег.
	 */
	protected function _html(): String
	{
		
		return '<' . $this->_tag . ' class="' . $this->_full_name . $this->_mod_str . $this->_mix_str . '"' . $this->_attr_str . '>' . $this->_content . '</' . $this->_tag . '>';
	}
	
	/**
	 *  Возвращает html код блока или элемента. Без закрывающего тега.
	 */
	protected function _html_(): String
	{
		
		return '<' . $this->_tag . ' class="' . $this->_full_name . $this->_mod_str . $this->_mix_str . '"' . $this->_attr_str . ' />';
	}

    /**
	 * Заполняет свойства необходимые для формирования любого html-блока.
	 *
	 * @param {array} $content
	 * 		Содержимое или контент.
	 */
	protected function _block( $content ): void
	{

		if ( $this->_name_current_block ) {

			// Для блока, имя текущего блока будет равно имени блока или элемента.
			$this->_name = $this->_name_current_block;
			$this->_full_name = $this->_name_current_block;

			$this->_reset();

			if ( is_array($content) and count($content) > 0 ) {

				foreach ($content as $value) {

					if ( gettype($value) == 'string' ) {

						$this->_content = $this->_content . $value;
					}
				}
			}

			$this->_generatorModClass();
			$this->_generatorMixClass();
			$this->_generatorContent();
			$this->_generatorAttr();
			$this->_ibem();
			$this->_tag();

			$this->_mod_str = implode( $this->_space, $this->_mod_arr);
			$this->_mix_str = implode( $this->_space, $this->_mix_arr );

			if ( count($this->_mod_arr) > 0 or count($this->_mix_arr) > 0 ) {

				$this->_full_name = $this->_full_name . $this->_space;
			}

			if ( count($this->_mod_arr) > 0 and count($this->_mix_arr) > 0 ) {

				$this->_mix_str = ' ' . $this->_mix_str;
			}

			if ( count($this->_attr) > 0 ) {

				$this->_attr[0] = $this->_space . $this->_attr[0];
			}
			$this->_attr_str = implode( $this->_space, $this->_attr );
		}
	}

	/**
	 * Заполняет свойства необходимые для формирования любого html-элемента.
	 *
	 * @param {array} $content
	 * 		Содержимое или контент.
	 */
	protected function _elem( $content, String $id = null ): void
	{

		if ( $this->_name_current_block ) {

			$this->_reset();

			if ( isset($id) ) {

				$this->_id = $id;
			}

			if ( is_array($content) and count($content) > 0 ) {

				foreach ($content as $value) {

					if ( gettype($value) == 'string' ) {

						$this->_content = $this->_content . $value;
					}
				}
			}

			$this->_generatorModClass();
			$this->_generatorMixClass();
			$this->_generatorContent();
			$this->_generatorAttr();
			$this->_ibem();
			$this->_tag();

			$this->_mod_str = implode( $this->_space, $this->_mod_arr);
			$this->_mix_str = implode( $this->_space, $this->_mix_arr );

			if ( count($this->_mod_arr) > 0 or count($this->_mix_arr) > 0 ) {

				$this->_full_name = $this->_full_name . $this->_space;
			}

			if ( count($this->_mod_arr) > 0 and count($this->_mix_arr) > 0 ) {

				$this->_mix_str = $this->_space . $this->_mix_str;
			}

			if ( count($this->_attr) > 0 ) {

				$this->_attr[0] = $this->_space . $this->_attr[0];
			}
			$this->_attr_str = implode( $this->_space, $this->_attr );
		}
	}

	/**
	 * Сообщает, что блоку или элементу необходимо показать: только содержимое внутри (false) или
	 * полностью весь блок или элемент (true).
	 *
	 * @return {bool}
	 */
	protected function _views(): bool
	{
		
		if ( isset($this->_show) ) {

			$arr_views = $this->_views[ "{$this->_show}" ];
			if ( in_array($this->_name, $arr_views) ) {

				return true;
			}
			else {

				foreach ($arr_views as $key => $value) {

					if ( is_array($value) and isset($this->_id) ) {

						foreach ($value as $key2 => $value2) {

							if ( $key2 == $this->_name ) {

								foreach ($value2 as $value3) {

									if ( preg_match('/' . $value3 . '/', $this->_id ) ) {

										return true;
									}
								}
							}
						}
					}
				}

				return false;
			}

		}
		else {

			return true;
		}
	}

	/**
	 * Динамический метод, который срабатывает при вызове несуществующего метода.
	 *
	 * @param {string} $method Имя вызываемого метода.
	 * @param {array} $arr Массив переданных параметров.
	 */
	public function __call( $method, $arr )
	{

		$type = strpos( $method, 'block' );
		$tag = str_replace('block', '', strtolower($method));

		// blockDiv, blockSpan, blockP, blockUl, blockSvg, blockPath
		if ( $type === 0 and in_array($tag, ['div', 'span', 'p', 'ul', 'svg', 'path']) ) {

			$this->_tag = $tag;
			$this->_name = $this->_name_current_block;
			$this->_full_name = $this->_name_current_block;

			if ( isset($arr[0]) ) {

				$this->_block( $arr[0] );
			}
			else {

				$this->_block( '' );
			}

			if ( !$this->_views() ) {

				return $this->_content;
			}

			return $this->_html();
		}

		// blockA
		if ( $type === 0 and $tag == 'a' ) {

			$this->_tag = $tag;
			$this->_name = $this->_name_current_block;
			$this->_full_name = $this->_name_current_block;

			if ( isset($arr[0]) ) {

				$this->_block( $arr[0] );
			}
			else {

				$this->_block( '' );
			}

			$this->_attr_element_cakephp[ "{$this->_name}" ]['class'] = $this->_full_name . $this->_mod_str . $this->_mix_str;

			if ( array_key_exists( 'url', $this->_attr_element_cakephp[ "{$this->_name}" ]) ) {

				$url = $this->_attr_element_cakephp[ "{$this->_name}" ]['url'];
				unset( $this->_attr_element_cakephp[ "{$this->_name}" ]['url'] );
			}
			else {

				$url = '#';
			}

			if ( !$this->_views() ) {
				
				return $this->_content;
			}

			return $this->Html->link( $this->_content, $url, $this->_attr_element_cakephp[ "{$this->_name}" ] ?? [] );
		}

		// blockForm
		if ( $type === 0 and $tag == 'form' ) {

			$this->_name = $this->_name_current_block;
			$this->_full_name = $this->_name_current_block;
			
			if ( isset($arr[0]) ) {

				$this->_block( $arr[0] );
			}
			else {

				$this->_block( '' );
			}

			$this->_attr_element_cakephp[ "{$this->_name}" ]['class'] = $this->_full_name . $this->_mod_str . $this->_mix_str;

			if ( !$this->_views() ) {
				
				return $this->_content;
			}

			return $this->Form->create( null, $this->_attr_element_cakephp[ "{$this->_name}" ] ) . $this->_content . $this->Form->end();
		}

		$type = strpos( $method, 'elem' );
		$tag = str_replace('elem', '', strtolower($method));

		// elemDiv, elemSpan, elemP, elemLi, elemSvg, elemPath, elemAnimateTransform, path.
		if ( $type === 0 and in_array($tag, ['div', 'span', 'p', 'li', 'svg', 'path', 'animatetransform']) ) {

			$this->_tag = $tag;
			$this->_name = $this->_name_current_block . '__' . $arr[0];
			$this->_full_name = $this->_name_current_block . '__' . $arr[0];

			if ( isset($arr[1]) ) {

				$this->_elem( $arr[1], $arr[2] ?? null );
			}
			else {

				$this->_elem( '', $arr[2] ?? null );
			}

			if ( !$this->_views() ) {
				
				return '';
			}

			return $this->_html();
		}
		
		// elemImg
		if ( $type === 0 and in_array($tag, ['img']) ) {
			
			$this->_tag = $tag;
			$this->_name = $this->_name_current_block . '__' . $arr[0];
			$this->_full_name = $this->_name_current_block . '__' . $arr[0];
			
			if ( isset($arr[1]) ) {

				$this->_elem( $arr[1], $arr[2] ?? null );
			}
			else {

				$this->_elem( '', $arr[2] ?? null );
			}
			
			if ( !$this->_views() ) {
				
				return '';
			}

			$this->_attr_element_cakephp[ "{$this->_name}" ]['class'] = $this->_full_name . $this->_mod_str . $this->_mix_str;
			
			if ( array_key_exists( 'url', $this->_attr_element_cakephp[ "{$this->_name}" ]) ) {

				$url = $this->_attr_element_cakephp[ "{$this->_name}" ]['url'];
				unset( $this->_attr_element_cakephp[ "{$this->_name}" ]['url'] );
			}
			else {

				$url = '#';
			}
			
			return $this->Html->image( $url, $this->_attr_element_cakephp[ "{$this->_name}" ] );
		}
		
		// elemButton
		if ( $type === 0 and in_array($tag, ['button']) ) {
			
			$this->_tag = $tag;
			$this->_name = $this->_name_current_block . '__' . $arr[0];
			$this->_full_name = $this->_name_current_block . '__' . $arr[0];

			if ( isset($arr[1]) ) {

				$this->_elem( $arr[1], $arr[2] ?? null );
			}
			else {

				$this->_elem( '', $arr[2] ?? null );
			}

			if ( !$this->_views() ) {
				
				return '';
			}

			$this->_attr_element_cakephp[ "{$this->_name}" ]['class'] = $this->_full_name . $this->_mod_str . $this->_mix_str;
			$this->_attr_element_cakephp[ "{$this->_name}" ]['type'] = 'button';
			$this->_attr_element_cakephp[ "{$this->_name}" ]['escapeTitle'] = false;

			return $this->Form->button( $this->_content, $this->_attr_element_cakephp[ "{$this->_name}" ] );
		}

		// virtBlockDiv, virtBlockSpan, virtBlockP, virtBlockI.
		$type = strpos( $method, 'virtBlock' );
		$tag = str_replace('virtblock', '', strtolower($method));

		if ( $type === 0 and in_array($tag, ['div', 'span', 'p', 'i']) ) {

			$mythis = $this->_virtBlock( $tag, $arr );

			if ( $tag == 'i' ) {

				return $mythis->_html();
			}
			if ( !$mythis->_views() ) {

				return $mythis->_content;
			}

			return $mythis->_html();
		}
		
		// virtBlockA
		if ( $type === 0 and $tag == 'a' ) {

			$mythis = $this->_virtBlock( $tag, $arr );
			$mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['class'] = $mythis->_full_name . $mythis->_mod_str . $mythis->_mix_str;

			if ( array_key_exists( 'url', $mythis->_attr_element_cakephp[ "{$mythis->_name}" ]) ) {

				$url = $mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['url'];
				unset( $mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['url'] );
			}
			else {

				$url = '#';
			}

			if ( !$mythis->_views() ) {
				
				return $mythis->_content;
			}

			return $mythis->Html->link( $mythis->_content, $url, $mythis->_attr_element_cakephp[ "{$mythis->_name}" ] ?? [] );
		}
		
		// virtBlockForm
		if ( $type === 0 and $tag == 'form' ) {

			$mythis = $this->_virtBlock( $tag, $arr );
			$mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['class'] = $mythis->_full_name . $mythis->_mod_str . $mythis->_mix_str;

			if ( !$mythis->_views() ) {
				
				return $mythis->_content;
			}

			return $mythis->Form->create( null, $mythis->_attr_element_cakephp[ "{$mythis->_name}" ] ) . $mythis->_content . $mythis->Form->end();
		}

		// virtBlockInput
		if ( $type === 0 and $tag == 'input' ) {
			
			$mythis = $this->_virtBlock( $tag, $arr );

			if ( $mythis->_mod_str and $mythis->_mix_str ) {

				$str = ' ' . $mythis->_mod_str . $mythis->_mix_str;
			}
			elseif ( !$mythis->_mod_str and $mythis->_mix_str ) {

				$str = ' ' . $mythis->_mix_str;
			}
			elseif ( $mythis->_mod_str and !$mythis->_mix_str ) {

				$str = ' ' . $mythis->_mod_str;
			}
			$mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['class'] = $mythis->_name . (empty($str) ? '' : $str);

			if ( !empty($mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['multiple']) and $mythis->_attr_element_cakephp[ "{$mythis->_name}" ]['multiple'] ) {

				return $mythis->Form->file( $mythis->_name . '[]', $mythis->_attr_element_cakephp[ "{$mythis->_name}" ] );
			}
			else {

				return $mythis->Form->file( $mythis->_name, $mythis->_attr_element_cakephp[ "{$mythis->_name}" ] );
			}
		}
	}
	
	/**
	 * Предварительно настраивает виртуальный блок.
	 * 
	 * @param {string} $tag
	 * 		Тип тега.
	 * @param {array} $arr
	 * 		Массив настроек блока.
	 * @return {object}
	 * 		Объект блока.
	 */
	protected function _virtBlock(String $tag, Array $arr ): object
	{
		$this->_tag = $tag;
		$this->_name = $arr[0];
		$this->_name_current_block = $arr[0];
		$this->_full_name = $this->_name_current_block;

		$object_bem = $this->_config( $arr[0], $arr[1] ?? [], $arr[2] ?? null );
		$object_bem['conf'][ "{$this->_name_current_block}" ]->_block( '' );

		return $object_bem['conf'][ "{$this->_name_current_block}" ];
	}

	/**
	 * Возвращает свойства в первоначальное состояние.
	 */
	protected function _reset(): void
	{
		$this->_mod_arr = [];
		$this->_mod_str = null;
		$this->_mix_arr = [];
		$this->_mix_str = null;
		$this->_content = '';
		$this->_attr = [];
	}
}
