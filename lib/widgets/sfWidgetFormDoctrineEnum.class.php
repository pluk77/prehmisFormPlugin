<?php
/**
 * Prehmis package
 *
 * PHP versions 5.3.1
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program in the file LICENSE.TXT;
 * if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package    Prehmis
 * @subpackage Widget
 * @author     Doug Smith <doug.smith@capetown.gov.za>
 * @author     Marcel Berteler <marcel@berteler.co.za>
 * @author     Kevin Cyster <kcyster@gmail.com>
 *
 * @copyright  2003-2010 by City of Cape Town, Cape Town, South Africa.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://www.capetown.gov.za/
 * @since      File available since Release 2.0
 *
 * @version    SNV: $Id: sfWidgetFormDoctrineEnum.class.php 3 2010-10-06 13:13:07Z marcel $
 */

/**
 * sfWidgetFormDoctrineEnum
 *
 * PHP versions 5.3.1
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program in the file LICENSE.TXT;
 * if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package    Prehmis
 * @subpackage Widget
 * @author     Doug Smith <doug.smith@capetown.gov.za>
 * @author     Marcel Berteler <marcel@berteler.co.za>
 * @author     Kevin Cyster <kcyster@gmail.com>
 *
 * @copyright  2003-2010 by City of Cape Town, Cape Town, South Africa.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://www.capetown.gov.za/
 * @since      File available since Release 2.0
 *
 * @version    SNV: $Id: sfWidgetFormDoctrineEnum.class.php 3 2010-10-06 13:13:07Z marcel $
 */
class sfWidgetFormDoctrineEnum extends sfWidgetFormChoice
{
  /**
   * Constructs the current widget.
   *
   * Available options:
   *
   * Available options:
   *
   *  * model:        The model class (required)
   *  * column:       The ENUM column of the model class (required)
   *
   * @see sfWidget
   * @param array $options    An array of options
   * @param array $attributes   An array of error messages
   * @return VOID
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = array();

    parent::__construct($options, $attributes);
  }

  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * model:        The model class (required)
   *  * column:       The ENUM column of the model class (required)
   *  * add_empty:    Whether to add a first empty value or not (false by default)
   *                  If the option is not a Boolean, the value will be used as the text value
   *  * remove        An array of values to remove from the dropdown
   * 
   * @see sfWidgetFormSelect
   * @param array $options An array of options
   * @param array $attributes An array of attributes
   * @return VOID
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('add_empty', false);
    $this->addOption('remove', array());

    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to ENUM column of the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();

    $enumValues = Doctrine_Core::getTable($this->getOption('model'))->getEnumValues($this->getOption('column'));
    $remove = $this->getOption('remove');
    if (!empty($remove))
    {
      foreach ($remove as $enumValue)
      {
        $key = array_search($enumValue, $enumValues);
        if ($key !== false)
        {
          unset($enumValues[$key]);
        }
      }
    }
    
    $choices = array_combine($enumValues, $enumValues);

    if (false !== $this->getOption('add_empty'))
    {
      $choices = array_merge(array('' => true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty')), $choices);
    }

    return $choices;
  }
}