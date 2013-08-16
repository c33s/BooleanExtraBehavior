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
        <column name="is_published" type="boolean" />

        <column name="has_budget_limit" type="boolean" />
        <column name="budget_limit" type="float" />

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

        $this->assertTrue(method_exists('BEUser', 'isPublished'));
        $this->assertTrue(method_exists('BEUser', 'setPublished'));
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

    /**
     * @depends testMutator
     */
    public function testCollidingColumnNames()
    {
        $user = new BEUser();

        $user->setHasBudgetLimit(true);
        $this->assertTrue($user->hasBudgetLimit());

        $user->setBudgetLimit(3.14);
        $this->assertEquals(3.14, $user->getBudgetLimit());
    }
}
