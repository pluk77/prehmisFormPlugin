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
 * @version    SNV: $Id: sfWidgetFormDateHidden.class.php 3 2010-10-06 13:13:07Z marcel $
 */

/**
 * sfWidgetFormDateHidden
 *
 * returned to show the ART intake details form
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
 * @version    SNV: $Id: sfWidgetFormDateHidden.class.php 3 2010-10-06 13:13:07Z marcel $
 */
class sfWidgetFormDateHidden extends sfWidgetForm
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('type');

    // to maintain BC with symfony 1.2
    $this->setOption('is_hidden', true);
    $this->setOption('type', 'hidden');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // convert value to an array
    $default = array('year' => null, 'month' => null, 'day' => null);
    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      $value = (string) $value == (string) (integer) $value ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }
    }

    $date = $this->renderDayWidget($name.'[day]', $value['day'], array(), array_merge($this->attributes, $attributes));
    $date .= $this->renderMonthWidget($name.'[month]', $value['month'], array(), array_merge($this->attributes, $attributes));
    $date .= $this->renderYearWidget($name.'[year]', $value['year'], array(), array_merge($this->attributes, $attributes));

    return $date;
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderDayWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputHidden($options, $attributes);
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderMonthWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputHidden($options, $attributes);
    return $widget->render($name, $value);
  }

  /**
   * @param string $name
   * @param string $value
   * @param array $options
   * @param array $attributes
   * @return string rendered widget
   */
  protected function renderYearWidget($name, $value, $options, $attributes)
  {
    $widget = new sfWidgetFormInputHidden($options, $attributes);
    return $widget->render($name, $value);
  }
}
