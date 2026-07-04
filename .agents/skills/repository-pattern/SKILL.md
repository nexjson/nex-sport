---
name: repository-pattern
description: "Apply this skill when creating, modifying, or reviewing Repository classes, Repository Interfaces, or RepositoryServiceProvider in this Laravel project. Triggers when: creating a new Repository, binding interfaces in a service provider, injecting repositories into controllers or services, building API controllers that share repository logic with web controllers, or when the user mentions 'repository', 'interface', 'contract', 'service provider binding', or 'dependency injection' in the context of data access."
---

# Repository Pattern — NEX-Sport

This project uses the Repository Pattern for six core entities. Always follow the conventions below exactly.

## Entities with Repositories

Only these entities use repositories. Everything else (Game, Organizer, Team, Reward) uses Eloquent directly.

| Entity | Interface | Implementation |
|--------|-----------|----------------|
| User | `UserRepositoryInterface` | `Eloquent/UserRepository` |
| Event | `EventRepositoryInterface` | `Eloquent/EventRepository` |
| Squad | `SquadRepositoryInterface` | `Eloquent/SquadRepository` |
| Registration | `RegistrationRepositoryInterface` | `Eloquent/RegistrationRepository` |
| Match | `MatchRepositoryInterface` | `Eloquent/MatchRepository` |
| RewardClaim | `RewardClaimRepositoryInterface` | `Eloquent/RewardClaimRepository` |

## Directory Structure

```
app/
├── Repositories/
│   ├── Contracts/
│   │   └── EventRepositoryInterface.php
│   └── Eloquent/
│       └── EventRepository.php
└── Providers/
    └── RepositoryServiceProvider.php
```

Register `RepositoryServiceProvider` in `bootstrap/providers.php`.

## Creating a Repository — Step by Step

### Step 1: Create the Interface

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface
{
    public function findById(int $id): ?Event;

    public function paginateForOrganizer(int $organizerId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Event;

    public function update(Event $event, array $data): Event;

    public function delete(Event $event): void;
}
```

### Step 2: Create the Eloquent Implementation

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EventRepository implements EventRepositoryInterface
{
    public function findById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function paginateForOrganizer(int $organizerId, int $perPage = 15): LengthAwarePaginator
    {
        return Event::where('organizer_id', $organizerId)
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): Event
    {
        $event->update($data);

        return $event->fresh();
    }

    public function delete(Event $event): void
    {
        $event->delete();
    }
}
```

### Step 3: Bind in RepositoryServiceProvider

```php
<?php

namespace App\Providers;

use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Eloquent\EventRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        // Add more bindings here as needed
    }
}
```

### Step 4: Inject in Controller via Constructor

**Web Controller (Inertia):**

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\EventRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $events,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Events/Index', [
            'events' => $this->events->paginateForOrganizer(
                auth()->user()->organizer->id
            ),
        ]);
    }
}
```

**API Controller (JSON for Mobile):**

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SquadResource;
use App\Repositories\Contracts\SquadRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SquadController extends Controller
{
    public function __construct(
        private readonly SquadRepositoryInterface $squads,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $squads = $this->squads->paginateForPlayer(auth()->id());

        return SquadResource::collection($squads);
    }
}
```

## Return Type Rules

| Query Type | Return Type |
|------------|-------------|
| Single record | `?ModelClass` |
| Multiple records | `Collection` or `LengthAwarePaginator` |
| Dashboard stats | `array` with named keys |
| Existence check | `bool` |

**Never return raw arrays from a repository when the result maps to a Model.**

## API Resource Convention

Every API controller response MUST use an API Resource. Never return raw models or arrays.

```php
// Create resource: php artisan make:resource SquadResource

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SquadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'game'       => $this->game->name,
            'members'    => $this->players->count(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
```

Standard API response wrapper — use `respondSuccess()` or just return resources directly (Laravel handles the `data` wrapper automatically for Resources).

## Testing Repositories

Mock the interface in controller tests; test the repository itself with `RefreshDatabase`.

```php
// Controller test — mock the interface
use App\Repositories\Contracts\SquadRepositoryInterface;

beforeEach(function () {
    $this->mock(SquadRepositoryInterface::class, function ($mock) {
        $mock->shouldReceive('paginateForPlayer')->andReturn(collect());
    });
});

// Repository test — use real database
uses(RefreshDatabase::class);

it('returns squads for the correct player', function () {
    $player = Player::factory()->create();
    Squad::factory()->count(3)->create(['leader_player_id' => $player->id]);

    $repo = new SquadRepository();
    $result = $repo->paginateForPlayer($player->id);

    expect($result)->toHaveCount(3);
});
```

## Sanctum API Authentication

All routes in `routes/api.php` under `/api/v1/` that require auth must use `auth:sanctum`:

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('squads', SquadController::class);
        Route::apiResource('players', PlayerController::class);
    });
});
```

Token creation on login:

```php
public function login(LoginRequest $request): JsonResponse
{
    // validate credentials...
    $token = $user->createToken('mobile-app')->plainTextToken;

    return response()->json([
        'data'    => new UserResource($user),
        'token'   => $token,
        'message' => 'Login successful.',
    ]);
}
```
