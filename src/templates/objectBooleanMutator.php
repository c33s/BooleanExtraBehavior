
/**
 * @param boolean $value
 *
 * @return <?php echo $objectClassName; ?>
 */
public function set<?php echo $methodName; ?>($value)
{
    return $this->set<?php echo $columnPhpName; ?>($value);
}
