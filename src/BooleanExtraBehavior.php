<?php

class BooleanExtraBehavior extends Behavior
{
    protected $tableModificationOrder = 40;

    protected $parameters = array(
        'prefixes' => array(
            'is',
            'has',
        )
    );

    public function objectMethods(PHP5ObjectBuilder $builder)
    {
        $script = '';
        $script .= $this->generateAccessorsAndMutators($builder);

        return $script;
    }

    protected function generateAccessorsAndMutators($builder)
    {
        $script = '';

        foreach ($this->getTable()->getColumns() as $column)
        {
            if (!$this->isBooleanColumn($column)) {
                continue;
            }

            foreach ($this->getPrefixes() as $prefix)
            {
                if( !$this->columnHasPrefix($prefix, $column))
                {
                    continue;
                }

                if ($this->columnHasConflictingBehavior($column))
                {
                    continue;
                }

                $script .= $this->buildAccessorCode($prefix, $column);

                $columnName = $this->buildAccessorMethodName($prefix, $column);

                if ($this->tableHasColumn($columnName)) {
                    continue;
                }
                $script .= $this->buildMutatorCode($prefix, $column, $builder);
            }
        }

        return $script;
    }

    protected function columnHasConflictingBehavior($column)
    {
        if ('ispublished' == strtolower($column->getPhpName()) && $this->getTable()->hasBehavior('publishable'))
        {
            return true;
        }

        return false;
    }

    protected function getPrefixes()
    {
        return $this->parameters['prefixes'];
    }

    protected function isBooleanColumn($column)
    {
        if (PropelTypes::BOOLEAN_NATIVE_TYPE === $column->getPhpType()) {
            return true;
        }

        return false;
    }

    protected function columnHasPrefix($prefix, $column)
    {
        if (0 !== strpos(lcfirst($column->getPhpName()), $prefix)) {
            return true;
        }

        return false;
    }

    protected function tableHasColumn($columnName)
    {
        if ($this->getTable()->getColumnByPhpName($columnName)) {
            return true;
        }
        return false;
    }

    protected function buildMutatorMethodName($prefix, $column)
    {
        $methodName = $this->buildAccessorMethodName($prefix, $column);

        return ucfirst(substr($methodName, strlen($prefix)));
    }

    protected function buildAccessorMethodName($prefix, $column)
    {
        return str_replace(ucfirst($prefix), $prefix, $column->getPhpName());
    }

    protected function buildAccessorCode($prefix, $column)
    {
        $methodName = $this->buildAccessorMethodName($prefix, $column);

        $script = $this->renderTemplate('objectBooleanAccessor', array(
            'methodName' => $methodName,
            'columnPhpName' => $column->getPhpName(),
        ));

        return $script;
    }

    protected function buildMutatorCode($prefix, $column, $builder)
    {
        $methodName = $this->buildMutatorMethodName($prefix, $column);

        $script = $this->renderTemplate('objectBooleanMutator', array(
            'methodName' => $methodName,
            'columnPhpName' => $column->getPhpName(),
            'objectClassName' => $builder->getStubObjectBuilder()->getClassname(),
        ));

        return $script;
    }
}
