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
 * @version    SNV: $Id: DoctrineUniqueWithNull.php 12 2010-11-16 15:29:59Z marcel $
 */

/**
 * DoctrineUniqueWithNull
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
 * @version    SNV: $Id: DoctrineUniqueWithNull.php 12 2010-11-16 15:29:59Z marcel $
 */
class DoctrineUniqueWithNull extends sfValidatorDoctrineUnique
{
  /**
   * Method to construct the class
   * @see sfValidatorSchema
   *
   * @param array $options An array of options
   * @param array $messages An array of error messages
   * @return VOID
   */
  public function __construct($options = array(), $messages = array())
  {
    parent::__construct($options, $messages);
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
    if (!is_array($this->getOption('column')))
    {
      $this->setOption('column', array($this->getOption('column')));
    }

    $column = $this->getOption('column');

    if($values[$column[0]] != '')
    {
      parent::doClean($values);
    }

    return $values;
  }
}