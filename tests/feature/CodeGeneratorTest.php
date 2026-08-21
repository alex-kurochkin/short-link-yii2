<?php

namespace tests\feature;

use tests\TestCase;
use app\models\Link;
use app\models\User;
use app\services\CodeGenerator;
use app\fixtures\UserFixture;
use app\fixtures\LinkFixture;
use Yii;

/**
 * Feature tests for CodeGenerator service
 */
class CodeGeneratorTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|app\contracts\CodeUniquenessChecker
     */
    private $checkerMock;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock for CodeUniquenessChecker
        $this->checkerMock = $this->createMock(app\contracts\CodeUniquenessChecker::class);
    }

    /**
     * Test that generator returns code of specified length
     */
    public function testGeneratorReturnsCodeOfSpecifiedLength(): void
    {
        $length = 6;
        
        $this->checkerMock->method('exists')
            ->willReturn(false);

        $generator = new CodeGenerator($this->checkerMock);
        $code = $generator->generate();

        $this->assertEquals($length, strlen($code));
    }

    /**
     * Test that generator uses code_length setting from config
     */
    public function testGeneratorUsesCodeLengthSettingFromConfig(): void
    {
        $length = 8;
        
        // Temporarily set the parameter
        $originalLength = Yii::$app->params['shortLinks']['codeLength'] ?? 6;
        Yii::$app->params['shortLinks']['codeLength'] = $length;
        
        $this->checkerMock->method('exists')
            ->willReturn(false);

        $generator = new CodeGenerator($this->checkerMock);
        $code = $generator->generate();
        
        // Restore original value
        Yii::$app->params['shortLinks']['codeLength'] = $originalLength;

        $this->assertEquals($length, strlen($code));
    }

    /**
     * Test that generator throws exception when max attempts exceeded
     */
    public function testGeneratorThrowsExceptionWhenMaxAttemptsExceeded(): void
    {
        $this->checkerMock->method('exists')
            ->willReturn(true);

        $generator = new CodeGenerator($this->checkerMock);

        $this->expectException(\RuntimeException::class);
        $generator->generate();
    }

    /**
     * Test that generator returns code that doesn't exist in storage
     */
    public function testGeneratorReturnsCodeThatDoesNotExistInStorage(): void
    {
        $this->checkerMock->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $generator = new CodeGenerator($this->checkerMock);
        $code = $generator->generate();

        $this->assertIsString($code);
        $this->assertEquals(6, strlen($code));
    }

    /**
     * Test that generator calls exists method on checker for uniqueness validation
     */
    public function testGeneratorCallsExistsMethodOnCheckerForUniquenessValidation(): void
    {
        $this->checkerMock->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        $generator = new CodeGenerator($this->checkerMock);
        $generator->generate();
    }

    /**
     * Test that generator makes multiple attempts on collisions
     */
    public function testGeneratorMakesMultipleAttemptsOnCollisions(): void
    {
        $this->checkerMock->expects($this->exactly(4))
            ->method('exists')
            ->willReturnOnConsecutiveCalls(true, true, true, false);

        $generator = new CodeGenerator($this->checkerMock);
        $code = $generator->generate();

        $this->assertIsString($code);
    }

    /**
     * Test that generator returns different codes on different calls
     */
    public function testGeneratorReturnsDifferentCodesOnDifferentCalls(): void
    {
        $this->checkerMock->method('exists')
            ->willReturn(false);

        $generator = new CodeGenerator($this->checkerMock);

        $code1 = $generator->generate();
        $code2 = $generator->generate();

        $this->assertNotEquals($code1, $code2);
    }
}
