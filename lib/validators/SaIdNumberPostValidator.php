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
 * @version    SNV: $Id: SaIdNumberPostValidator.php 13 2010-11-18 14:13:45Z marcel $
 */

/**
 * SaIdNumberPostValidator
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
 * @version    SNV: $Id: SaIdNumberPostValidator.php 13 2010-11-18 14:13:45Z marcel $
 */
class SaIdNumberPostValidator extends sfValidatorSchema
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * date_of_birth:         The date_of_birth field name
   *  * gender:                The gender field name
   *  * sa_id_number:          The sa_id_number field name
   *  * throw_global_error:    Whether to throw a global error (false by default) or an error tied to the left field
   *
   * @param string $date_of_birth   The date_of_birth name
   * @param string $gender          The gender field name
   * @param string $sa_id_number    The sa_id_number field name
   * @param array  $options     An array of options
   * @param array  $messages    An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($options = array(), $messages = array())
  {
    $this->addRequiredOption('dob');
    $this->addRequiredOption('sex');
    $this->addRequiredOption('saId');

    $this->addOption('throw_global_error', false);

    parent::__construct(null, $options, $messages);
  }

  /**
   * Method to clean the values
   * @see sfValidatorBase
   *
   * @param array $values The array of "dirty" values
   * @return array/sfValidatorError $values The array of clean values on success / sfValidatorError on failure
   */
  protected function doClean($values)
  {
    if (null === $values)
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    // set up variables
    $dateOfBirth  = isset($values[$this->getOption('dob')]) ? $values[$this->getOption('dob')] : null;
    $gender = isset($values[$this->getOption('sex')]) ? $values[$this->getOption('sex')] : null;
    $saIdNumber  = isset($values[$this->getOption('saId')]) ? $values[$this->getOption('saId')] : null;

    $valid = true;
    // if ID number not given pass validation
    if (!empty($saIdNumber))
    {
      // both date of birth and gender given - do validation
      if (!empty($dateOfBirth) && !empty($gender))
      {
        // ID number not numeric - fail validation
        if (!is_numeric($saIdNumber))
        {
          $valid = false;
          $error = new sfValidatorError($this, 'The id number must be numeric', array());
        // ID number not not 13 digits long - fail validation
        }
        elseif (trim(strlen($saIdNumber)) != 13)
        {
          $valid = false;
          $error = new sfValidatorError($this, 'The id number must be 13 characters long', array());
        // validate date of birth, gender and control digit
        }
        else
        {
          // set up variables
          $dob = (string) date('ymd', strtotime($dateOfBirth));
          $idDob = substr($saIdNumber, 0, 6);
          $idGen = substr($saIdNumber, 6, 1);
          $con = (string) $this->getIdControlDigit($saIdNumber);
          $idCon = substr($saIdNumber, 12, 1);
          // ID dob != dob - fail validation
          if ($dob != $idDob)
          {
            $valid = false;
            $error = new sfValidatorError($this, 'The date of birth does not match the date of birth of the id number', array());
          // ID gender != gender - fail validation
          }
          elseif (($idGen >= 0 && $idGen <= 4) && $gender != 'Female')
          {
            $valid = false;
            $error = new sfValidatorError($this, 'The gender does not match the gender of the id number', array());
          // ID gender != gender - fail validation
          }
          elseif (($idGen >= 5 && $idGen <= 9) && $gender != 'Male')
          {
            $valid = false;
            $error = new sfValidatorError($this, 'The gender does not match the gender of the id number', array());
          // ID control != control - fail validation
          }
          elseif ($con != $idCon)
          {
            $valid = false;
            $error = new sfValidatorError($this, 'The id number is not valid', array());
          }
        }
      // if only gender given - fail validation
      }
      elseif (empty($dateOfBirth))
      {
        $valid = false;
        $error = new sfValidatorError($this, 'The date of birth is required', array());
      // if only date of birth given - fail validation
      }
      elseif (empty($gender))
      {
        $valid = false;
        $error = new sfValidatorError($this, 'The gender is required', array());
      // no date of birth and no gender given - fail validation
      }
      else
      {
        $valid = false;
        $error = new sfValidatorError($this, 'Both the date of birth and the gender are required', array());
      }
    }
    else
    {
      $values[$this->getOption('saId')]=null;
    }

    if (!$valid)
    {
      if ($this->getOption('throw_global_error'))
      {
        throw $error;
      }

      throw new sfValidatorErrorSchema($this, array($this->getOption('saId') => $error));
    }

    return $values;
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
    for ($i = 0; $i < 6; $i++)
    {
      $a += $id{2*$i};
    }

    $b = 0;
    for ($i = 0; $i < 6; $i++)
    {
      $b = $b*10 + $id{2*$i+1};
    }
    $b *= 2;

    $c = 0;
    do
    {
      $c += $b % 10;
      $b = $b / 10;
    }
    while($b > 0);

    $c += $a;
    $d = 10 - ($c % 10);
    if ($d == 10)
    {
      $d = 0;
    }
    
    return $d;
  }
}
