<?php

/*
 * This file is a modified version of the sfDoctrineFormGenerator
 * class which is part of the symfony package.
 *
 * original auhtors:
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full original copyright and license information, please view the LICENSE
 * file that was distributed with the original source code.
 */

/**
 * prehmis Doctrine form generator.
 *
 * This class generates a Doctrine forms.
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
 * @package    prehmisFormPlugin
 * @subpackage generator
 * @author     Marcel Berteler <marcel@berteler.co.za>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    SVN: $Id: sfDoctrineFormGeneratorEgov.class.php 8 2010-10-13 16:42:43Z marcel $
 */
class sfDoctrineFormGeneratorEgov extends sfDoctrineFormGenerator
{

  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'string':
        $widgetSubclass = null === $column->getLength() || $column->getLength() > 255 ? 'Textarea' : 'InputText';
        break;
      case 'boolean':
        $widgetSubclass = 'InputCheckbox';
        break;
      case 'blob':
      case 'clob':
        $widgetSubclass = 'Textarea';
        break;
      case 'date':
        $widgetSubclass = 'Date';
        break;
      case 'time':
        $widgetSubclass = 'Time';
        break;
      case 'timestamp':
        $widgetSubclass = 'DateTime';
        break;
      case 'enum':
        $widgetSubclass = 'DoctrineEnum';
        break;
      default:
        $widgetSubclass = 'InputText';
    }

    if ($column->isPrimaryKey())
    {
      $widgetSubclass = 'InputHidden';
    }
    else if ($column->isForeignKey())
    {
      $widgetSubclass = 'DoctrineChoice';
    }

    return sprintf('sfWidgetForm%s', $widgetSubclass);
  }

  /**
   * Returns a PHP string representing options to pass to a widget for a given column.
   *
   * @param sfDoctrineColumn $column
   * 
   * @return string The options to pass to the widget as a PHP string
   */
  public function getWidgetOptionsForColumn($column)
  {
    $options = array();

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => $this->getRelatedModelName(\'%s\'), \'add_empty\' => \'--Select %s--\' ', $column->getRelationKey('alias'), sfInflector::humanize($column->getName()));
    }
    else if ('enum' == $column->getDoctrineType() && is_subclass_of($this->getWidgetClassForColumn($column), 'sfWidgetFormChoiceBase'))
    {
      $options[] = sprintf('\'model\' => $this->getModelName(), \'column\'=>\'%s\', \'add_empty\' => \'--Select %s--\' ', $column->getName(), sfInflector::humanize($column->getName()));
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $validatorSubclass = 'Boolean';
        break;
      case 'string':
    		if ($column->getDefinitionKey('email'))
    		{
    		  $validatorSubclass = 'Email';
    		}
    		else if ($column->getDefinitionKey('regexp'))
    		{
    		  $validatorSubclass = 'Regex';
    		}
    		else
    		{
    		  $validatorSubclass = 'String';
    		}
        break;
      case 'clob':
      case 'blob':
        $validatorSubclass = 'String';
        break;
      case 'float':
      case 'decimal':
        $validatorSubclass = 'Number';
        break;
      case 'integer':
        $validatorSubclass = 'Integer';
        break;
      case 'date':
        $validatorSubclass = 'Date';
        break;
      case 'time':
        $validatorSubclass = 'Time';
        break;
      case 'timestamp':
        $validatorSubclass = 'DateTime';
        break;
      case 'enum':
        $validatorSubclass = 'DoctrineEnum';
        break;
      default:
        $validatorSubclass = 'Pass';
    }

    if ($column->isForeignKey())
    {
      $validatorSubclass = 'DoctrineChoice';
    }
    else if ($column->isPrimaryKey())
    {
      $validatorSubclass = 'Choice';
    }

    return sprintf('sfValidator%s', $validatorSubclass);
  }

  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param sfDoctrineColumn $column
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getValidatorOptionsForColumn($column)
  {
    $options = array();
    $messages = null;

    $messages = sprintf('\'required\' =>\'Please provide %s\' ', sfInflector::humanize($column->getName()));

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => $this->getRelatedModelName(\'%s\')', $column->getRelationKey('alias'));
    }
    else if ($column->isPrimaryKey())
    {
      $options[] = sprintf('\'choices\' => array($this->getObject()->get(\'%s\')), \'empty_value\' => $this->getObject()->get(\'%1$s\')', $column->getFieldName());
    }
    else
    {
      switch ($column->getDoctrineType())
      {
        case 'string':
          if ($column['length'])
          {
            $options[] = sprintf('\'max_length\' => %s', $column['length']);
          }
          if (isset($column['minlength']))
          {
            $options[] = sprintf('\'min_length\' => %s', $column['minlength']);
          }
          if (isset($column['regexp']))
          {
            $options[] = sprintf('\'pattern\' => \'%s\'', $column['regexp']);
          }
          break;
        case 'enum':
          $options[] = sprintf('\'model\' => $this->getModelName(), \'column\'=>\'%s\' ', $column->getName());
          $messages = sprintf('\'required\' =>\'Please select %s\' ', sfInflector::humanize($column->getName()));
          break;
      }
    }

    // If notnull = false, is a primary or the column has a default value then
    // make the widget not required
    if (!$column->isNotNull() || $column->isPrimaryKey() || $column->hasDefinitionKey('default'))
    {
      $options[] = '\'required\' => false';
    }

    $return = count($options) ? sprintf('array(%s), ', implode(', ', $options)) : '';
    $return .= isset($messages) ? sprintf('array(%s)', $messages) : '';

    return $return;
  }

  
}
