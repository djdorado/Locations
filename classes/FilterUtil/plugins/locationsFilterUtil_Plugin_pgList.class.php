<?php
/**
 * locations
 *
 * @copyright (c) 2008,2010, Locations Development Team
 * @link http://code.zikula.org/locations
 * @author Steffen Voß
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package locations
 */

Loader::loadClass('locationsFilterUtil_OpCommon', LOCATIONS_FILTERUTIL_CLASS_PATH);

class locationsFilterUtil_Plugin_pgList extends locationsFilterUtil_OpCommon
{
    /**
     * Constructor
     *
     * @access public
     * @param array $config Configuration
     * @return object locationsFilterUtil_Plugin_pgList
     */
    public function __construct($config)
    {
        if (isset($config['fields']) && is_array($config['fields']))
        $this->addFields($config['fields']);

        parent::__construct($config);

        return $this;
    }

    public function availableOperators()
    {
        return array('sub');
    }

    /**
     * Add fields to list
     *
     * @access public
     * @param array $config Configuration
     * @return void
     */
    public function addFields($fields)
    {
        foreach ($fields as $field => $lid) {
            $this->fields[$field] = $lid;
        }
    }

    /**
     * Get fields
     *
     * @access public
     * @return array Array of fields
     */
    function getFields()
    {
        $fields = array_keys($this->fields);
        return $fields;
    }

    /**
     * return SQL code
     *
     * @access public
     * @param string $field Field name
     * @param string $myfield SQL Field name
     * @param string $op Operator
     * @param string $value Test value
     * @return string SQL code
     */
    function getSQL($field, $myfield, $op, $value)
    {
        if ($op != 'sub' || !isset($this->fields[$field])) {
            return '';
        }

        $list = pnModAPIFunc('pagesetter', 'admin', 'getList', array('lid'    => $this->fields[$field], 'topListValueID' => $value));

        if ($list === false) {
            pnSessionDelVar('errormsg');
            return '';
        }

        $items = array();
        foreach ($list['items'] as $item) {
            $items[] = $item['id'];
        }
        if (count($items) == 1)
        $where = "$myfield = " . implode("", $items);
        else
        $where = "$myfield IN (" . implode(",", $items) . ")";
        return concat('where');
    }
}
