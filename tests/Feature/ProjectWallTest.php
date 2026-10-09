<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Support\ProjectWall;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectWallTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteContentSeeder::class, RateCardSeeder::class]);
    }

    public function test_home_shows_the_wall_with_project_tiles_linked_to_the_viewer(): void
    {
        $projects = Project::factory()->count(3)->create();

        $response = $this->get('/')->assertOk()
            ->assertSee('data-project-wall', false)
            ->assertSee('Every frame,')
            ->assertSee('data-direction="up"', false)
            ->assertSee('data-direction="down"', false)
            ->assertSee('aria-hidden="true" inert', false);

        foreach ($projects as $project) {
            $response->assertSee('class="wall__card" data-open-project="'.$project->slug.'"', false);
        }
    }

    public function test_wall_includes_non_featured_projects_in_the_viewer_data(): void
    {
        Project::factory()->featured()->create(['title' => 'Featured film']);
        Project::factory()->create(['title' => 'Unfeatured film', 'slug' => 'unfeatured-film']);

        $this->get('/')->assertSee('data-open-project="unfeatured-film"', false)->assertSee('"slug":"unfeatured-film"', false);
    }

    public function test_wall_is_omitted_without_published_work(): void
    {
        Project::factory()->draft()->create();

        $this->get('/')->assertOk()->assertDontSee('data-project-wall', false);
    }

    public function test_columns_are_padded_and_rotated(): void
    {
        $projects = Project::factory()->count(2)->create()->load('media');

        $columns = ProjectWall::columns($projects);

        $this->assertCount(5, $columns);
        foreach ($columns as $tiles) {
            $this->assertCount(7, $tiles);
        }
        $this->assertNotSame($columns[0][0]['slug'], $columns[1][0]['slug']);
        $this->assertNotSame($columns[1][0]['slug'], $columns[2][0]['slug']);
    }
}
