<?php

class BooleanExtraBehavior extends Behavior
{
    public function objectMethods(PHP5ObjectBuilder $builder)
    {
        $script = '';

        foreach ($this->getTable()->getColumns() as $eachColumn) {
            if (PropelTypes::BOOLEAN_NATIVE_TYPE !== $eachColumn->getPhpType()) {
                continue;
            }

            $prefixes = array(
                'is',
                'has',
            );

            foreach ($prefixes as $eachPrefix) {
                if (0 !== strpos(lcfirst($eachColumn->getPhpName()), $eachPrefix)) {
                    continue;
                }

                $methodName = str_replace(ucfirst($eachPrefix), $eachPrefix, $eachColumn->getPhpName());

                $script .= $this->renderTemplate('objectBooleanAccessor', array(
                    'methodName' => $methodName,
                    'columnPhpName' => $eachColumn->getPhpName(),
                ));

                $methodName = ucfirst(str_replace($eachPrefix, '', $methodName));
                if ($this->getTable()->getColumnByPhpName($methodName)) {
                    continue;
                }

                $script .= $this->renderTemplate('objectBooleanMutator', array(
                    'methodName' => ucfirst(str_replace($eachPrefix, '', $methodName)),
                    'columnPhpName' => $eachColumn->getPhpName(),
                    'objectClassName' => $builder->getStubObjectBuilder()->getClassname(),
                ));
            }
        }

        return $script;
    }
}
