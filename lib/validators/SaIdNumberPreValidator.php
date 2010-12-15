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
 * @subpackage Validator
 * @author     Doug Smith <doug.smith@capetown.gov.za>
 * @author     Marcel Berteler <marcel@berteler.co.za>
 * @author     Kevin Cyster <kcyster@gmail.com>
 *
 * @copyright  2003-2010 by City of Cape Town, Cape Town, South Africa.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://www.capetown.gov.za/
 * @since      File available since Release 2.0
 *
 * @version    SNV: $Id: SaIdNumberPreValidator.php 3 2010-10-06 13:13:07Z marcel $
 */

/**
 * SaIdNumberPreValidator
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
 * @subpackage Validator
 * @author     Doug Smith <doug.smith@capetown.gov.za>
 * @author     Marcel Berteler <marcel@berteler.co.za>
 * @author     Kevin Cyster <kcyster@gmail.com>
 *
 * @copyright  2003-2010 by City of Cape Town, Cape Town, South Africa.
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link       http://www.capetown.gov.za/
 * @since      File available since Release 2.0
 *
 * @version    SNV: $Id: SaIdNumberPreValidator.php 3 2010-10-06 13:13:07Z marcel $
 */
class SaIdNumberPreValidator extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * dob: The dob of the person
   *  * gender: The gender of the person: m / f
   *
   * Available error codes:
   *
   *  * dob
   *  * gender
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', '"%value%" is not a valid SA ID Number.');
  }

  /**
   * Method to clean the values
   * @see sfValidatorBase
   *
   * @param array $values The array of "dirty" values
   * @return array/sfValidatorError $values The array of clean values on success / sfValidatorError on failure
   */
  protected function doClean($value)
  {
    if (!is_numeric($value))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    $match = preg_match ("!^(\d{2})(\d{2})(\d{2})(\d{1})\d{6}$!", $value, $matches);
    if (!$match) {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    if ($value{strlen($value)-1} != $this->getIdControlDigit($value)) {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    if($year == '00') $year = '2000';

    list (, $year, $month, $day, $gender) = $matches;

    if (!checkdate($month,$day,$year)) {
		throw new sfValidatorError($this, 'invalid', array('value' => $value));
	}

    return $value;
  }

  /**
   * This method returns the sa id number control digit
   *
   * @param string $id The id number to check
   * @return integer $d The control digit
   */
  private static function getIdControlDigit($id)
  {
    $d = -1;

    $a = 0;
    for($i = 0; $i < 6; $i++)
    {
      $a += $id{2*$i};
    }

    $b = 0;
    for($i = 0; $i < 6; $i++)
    {
      $b = $b*10 + $id{2*$i+1};
    }
    $b *= 2;

    $c = 0;
    do
    {
      $c += $b % 10;
      $b = $b / 10;
    } while($b > 0);

    $c += $a;
    $d = 10 - ($c % 10);
    if($d == 10) $d = 0;

    return $d;
  }
}
