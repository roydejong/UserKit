<?php

namespace UserKit\Runtime;

use ActiveRecord\Connection;
use ActiveRecord\RecordNotFound;

/**
 * UserKit base implementation of an ActiveRecord model.
 */
class Model extends \ActiveRecord\Model
{
    /**
     * Using the currently specified arguments on this model, tries to query the database for an exact match.
     *
     * @return $this|Model|null Database model if data found, otherwise null
     */
    public function getExisting(): Model
    {
        $conditions = [];

        foreach ($this->dirty_attributes() as $key => $value) {
            $conditionsKey = &$conditions[0];

            if (strlen($conditionsKey) > 0) {
                $conditionsKey .= ' AND ';
            }

            $conditionsKey .= $key;

            if ($value === null) {
                $conditionsKey .= ' IS NULL';
            } else {
                $conditionsKey .= ' = ?';
                $conditions[] = $value;
            }
        }

        try {
            $existing = $this->find(['conditions' => $conditions]);

            if ($existing != null)
                return $existing;
        } catch (RecordNotFound $e) {
        }

        return null;
    }

    /**
     * Formats a DateTime key within this model instance.
     *
     * @param string $dateKey
     * @return string
     */
    public function formatDate(string $dateKey): string
    {
        return $this->$dateKey->foprmat(Connection::$datetime_format);
    }
}