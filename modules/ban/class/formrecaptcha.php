<?php
/**
 * Xortify Bans & Unbans Function
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Coopertive (Australia)  http://web.labs.coop
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         bans
 * @subpackage		ban
 * @author         	Simon Roberts (wishcraft) - meshy@labs.coop
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');

/* Recaptcha Class */
include_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR . 'recaptcha' . DIRECTORY_SEPARATOR .  'recaptchalib.php';

/**
 * A text Recaptcha
 *
 * @author 		Kazumi Ono <onokazu@xoops.org>
 * @author 		John Neill <catzwolf@xoops.org>
 * @author 		Simon Roberts <simon@xoops.org>
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/ 
 * @package 	kernel
 * @subpackage 	form
 * @access 		public
 */
class banFormRecaptcha extends XoopsFormElement
{
    /**
     * Text
     *
     * @var string
     * @access private
     */
    var $_value;
    
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $value Text
     */
    function __construct($caption = '', $public_key = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_value = recaptcha_get_html($public_key);
    }
    
    /**
     * Get the "value" attribute
     *
     * @param bool $encode To sanitizer the text?
     * @return string
     */
    function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }
    
    /**
     * Prepare HTML for output
     *
     * @return string
     */
    function render()
    {
        return $this->getValue();
    }
}

?>