<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\TransactionPolicy;
use PHPUnit\Framework\TestCase;

class TransactionPolicyTest extends TestCase
{
    private TransactionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TransactionPolicy;
    }

    public function test_owner_can_view_update_delete(): void
    {
        $user = new User;
        $user->id = 1;
        $transaction = new Transaction(['user_id' => 1]);

        $this->assertTrue($this->policy->view($user, $transaction));
        $this->assertTrue($this->policy->update($user, $transaction));
        $this->assertTrue($this->policy->delete($user, $transaction));
        $this->assertTrue($this->policy->restore($user, $transaction));
    }

    public function test_other_user_cannot_view_update_delete(): void
    {
        $user = new User;
        $user->id = 2;
        $transaction = new Transaction(['user_id' => 1]);

        $this->assertFalse($this->policy->view($user, $transaction));
        $this->assertFalse($this->policy->update($user, $transaction));
        $this->assertFalse($this->policy->delete($user, $transaction));
        $this->assertFalse($this->policy->restore($user, $transaction));
    }

    public function test_force_delete_always_false(): void
    {
        $user = new User;
        $user->id = 1;
        $transaction = new Transaction(['user_id' => 1]);

        $this->assertFalse($this->policy->forceDelete($user, $transaction));
    }

    public function test_view_any_and_create_always_true_for_authenticated_user(): void
    {
        $user = new User;
        $user->id = 1;

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->create($user));
    }
}
