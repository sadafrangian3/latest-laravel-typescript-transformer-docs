<?php

namespace Spatie\TypescriptTransformer\Tests\Actions;

use PHPUnit\Framework\TestCase;
use Spatie\TypescriptTransformer\Steps\ReplaceMissingSymbolsStep;
use Spatie\TypescriptTransformer\Structures\Collection;
use Spatie\TypescriptTransformer\Tests\FakeClasses\FakeType;

class ReplaceMissingSymbolsStepTest extends TestCase
{
    private ReplaceMissingSymbolsStep $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new ReplaceMissingSymbolsStep();
    }

    /** @test */
    public function it_can_replace_missing_symbols()
    {
        $collection = Collection::create()
            ->add(
                FakeType::create('Dto')
                    ->withTransformed('{enum: {%enums\Enum%}, non-existing: {%non-existing%}}')
                    ->withMissingSymbols([
                        'enum' => 'enums\Enum',
                        'non-existing' => 'non-existing',
                    ])
            )
            ->add(
                FakeType::create('Enum')->withNamespace('enums')
            );

        $collection = $this->action->execute($collection);

        $types = $collection->getStructure()->getTypes();

        $this->assertCount(1, $types);
        $this->assertEquals('{enum: Enum, non-existing: any}', $types['Dto']->transformed);
    }
}