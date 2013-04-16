<?php

class BooleanExtraBehaviorTest extends PHPUnit_Framework_TestCase
{
    protected static $initialized = false;

    protected function setUp()
    {
        if (static::$initialized) {
            return;
        }

        static::$initialized = true;

        $builder = new PropelQuickBuilder();

        $config  = $builder->getConfig();
        $config->setBuildProperty('behavior.boolean_extra.class', '../src/BooleanExtraBehavior');

        $builder->setConfig($config);
        $builder->setSchema($this->getSchema());

        $builder->build();
    }

    protected function getSchema()
    {
        return <<<XML
<database name="default" defaultIdMethod="native">
    <table name="user" phpName="BEUser">
        <column name="id" type="integer" autoIncrement="true" primaryKey="true" />
        <column name="email" type="varchar" size="255" required="true" primaryString="true" />

        <column name="is_active" type="boolean" />

        <behavior name="boolean_extra" />
    </table>
</database>
XML;
    }

    public function testSetupIsFine()
    {
        $this->assertTrue(class_exists('BEUser'),
            'The schema has been loaded correctly.');
    }

    /**
     * @depends testSetupIsFine
     */
    public function testMethodHasBeenGenerated()
    {
        $this->assertTrue(method_exists('BEUser', 'isActive'));
        $this->assertTrue(method_exists('BEUser', 'setActive'));
    }

    /**
     * @depends testMethodHasBeenGenerated
     */
    public function testAccessor()
    {
        $user = new BEUser();

        $user->setIsActive(false);
        $this->assertFalse($user->isActive());

        $user->setIsActive(true);
        $this->assertTrue($user->isActive());
    }

    /**
     * @depends testMethodHasBeenGenerated
     * @depends testAccessor
     */
    public function testMutator()
    {
        $user = new BEUser();

        $user->setActive(false);
        $this->assertFalse($user->isActive());

        $user->setActive(true);
        $this->assertTrue($user->isActive());
    }
}
