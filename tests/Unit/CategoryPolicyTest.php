<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use App\Policies\CategoryPolicy;
use PHPUnit\Framework\TestCase;

class CategoryPolicyTest extends TestCase
{
    private CategoryPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CategoryPolicy;
    }

    public function test_any_user_can_view_global_category(): void
    {
        $user = new User;
        $user->id = 99;
        $global = new Category(['user_id' => null, 'is_editable' => false]);

        $this->assertTrue($this->policy->view($user, $global));
    }

    public function test_user_can_view_own_private_category(): void
    {
        $user = new User;
        $user->id = 1;
        $category = new Category(['user_id' => 1, 'is_editable' => true]);

        $this->assertTrue($this->policy->view($user, $category));
    }

    public function test_user_cannot_view_another_users_private_category(): void
    {
        $user = new User;
        $user->id = 2;
        $category = new Category(['user_id' => 1, 'is_editable' => true]);

        $this->assertFalse($this->policy->view($user, $category));
    }

    public function test_owner_can_update_delete_when_editable(): void
    {
        $user = new User;
        $user->id = 1;
        $category = new Category(['user_id' => 1, 'is_editable' => true]);

        $this->assertTrue($this->policy->update($user, $category));
        $this->assertTrue($this->policy->delete($user, $category));
        $this->assertTrue($this->policy->restore($user, $category));
    }

    public function test_non_owner_cannot_update_delete_private_category(): void
    {
        $user = new User;
        $user->id = 2;
        $category = new Category(['user_id' => 1, 'is_editable' => true]);

        $this->assertFalse($this->policy->update($user, $category));
        $this->assertFalse($this->policy->delete($user, $category));
    }

    public function test_global_category_cannot_be_updated_or_deleted_even_by_null_owner_convention(): void
    {
        $user = new User;
        $user->id = 1;
        $global = new Category(['user_id' => null, 'is_editable' => false]);

        $this->assertFalse($this->policy->update($user, $global));
        $this->assertFalse($this->policy->delete($user, $global));
    }

    public function test_owner_cannot_update_delete_when_not_editable(): void
    {
        $user = new User;
        $user->id = 1;
        $category = new Category(['user_id' => 1, 'is_editable' => false]);

        $this->assertFalse($this->policy->update($user, $category));
        $this->assertFalse($this->policy->delete($user, $category));
    }

    public function test_force_delete_always_false(): void
    {
        $user = new User;
        $user->id = 1;
        $category = new Category(['user_id' => 1, 'is_editable' => true]);

        $this->assertFalse($this->policy->forceDelete($user, $category));
    }
}
