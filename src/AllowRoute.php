<?php

declare(strict_types=1);

namespace Medas\Routing;

use Medas\Core\Events\BasicVote;

/**
 * Dispatched once per discovered route, before it's added to the compiled handler list. Listeners
 * can veto a route by setting $allowedAccess to AllowedAccess::Denied (eg. voteAdminOnly()-style
 * helpers, or a plain assignment). The default, untouched state (AllowedAccess::Pending) is treated
 * as allowed - routes are on by default, and nothing has to listen for this event for routing to
 * work exactly as it does today.
 */
class AllowRoute extends BasicVote
{
    public function __construct(
        public readonly RouteHandler $handler,
    )
    {
    }
}
