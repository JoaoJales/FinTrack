<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use App\Policies\AccountPolicy;
use PHPUnit\Framework\TestCase;

class AccountPolicyTest extends TestCase
{
    private AccountPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AccountPolicy;
    }

    public function test_owner_can_view_update_delete(): void
    {
        $user = new User;
        $user->id = 1;
        $account = new Account(['user_id' => 1]);

        $this->assertTrue($this->policy->view($user, $account));
        $this->assertTrue($this->policy->update($user, $account));
        $this->assertTrue($this->policy->delete($user, $account));
        $this->assertTrue($this->policy->restore($user, $account));
    }

    public function test_other_user_cannot_view_update_delete(): void
    {
        $user = new User;
        $user->id = 2;
        $account = new Account(['user_id' => 1]);

        $this->assertFalse($this->policy->view($user, $account));
        $this->assertFalse($this->policy->update($user, $account));
        $this->assertFalse($this->policy->delete($user, $account));
        $this->assertFalse($this->policy->restore($user, $account));
    }

    public function test_force_delete_always_false(): void
    {
        $user = new User;
        $user->id = 1;
        $account = new Account(['user_id' => 1]);

        $this->assertFalse($this->policy->forceDelete($user, $account));
    }

    public function test_view_any_and_create_always_true(): void
    {
        $user = new User;
        $user->id = 1;

        $this->assertTrue($this->policy->viewAny($user));
        $this->assertTrue($this->policy->create($user));
    }
}
