# Par'Axis — Events & Workshops module (Template integration workshop)

Module entities: **Event** + **Event_Registration** (`event_registration.event_id` → `event.id`,
`event_registration.user_id` → `user.id`). Deleting an event deletes its registrations.
Same architecture as the team's *catalog*, *health-advice*, *orders-payments* and *customer-support* modules:
**one entry point** (`index.php?page=...`) for the front office AND the back office, routes
`page => [Controller, method]`, two layouts, page views without any `require_once`. No database yet.
The layouts, partials, includes, CSS and JS are the **same files as the other modules**; only the module pages change.

## Structure

```
index.php                          front controller + autoloader (index.php?page=...)
app/routes.php                     page key => [Controller, method]
app/helpers.php                    isActive(), e(), currentUser(), loginUser(), isAdmin(), fmtDate(), flash(), takeFlash()
app/Controllers/
  Controller.php                   abstract base: render(), capture(), redirect(), isPost(), requireAdmin()
  EventsController.php             FRONT  : index (events + filter + search), register, mine (my registrations), cancel
  AuthController.php               login, signup, logout (?next=my-registrations supported)
  EventsAdminController.php        BACK   : index (stats + list + form), save (create / edit), delete
  RegistrationsController.php      BACK   : index (filter by event / status), toggle (cancel / re-register), delete
  PlaceholderController.php        empty pages for the other team members
app/Models/
  Model.php                        all(), find(), where(), insert(), update(), delete() — session storage
  Event.php                        starting rows + search(), taken(), isFull(), isPast(), status(), deleteWithRegistrations()
  EventRegistration.php            starting rows + forEvent(), forUser(), mine(), filter(), countRegistered()
  User.php                         admin account + sign-up accounts
app/Views/
  front-layout.php                 front office: head, spinner, navbar, content, footer, scripts
  layout.php                       back office : head (+ preloader), sidebar, header, content, footer, scripts
  includes/                        PART 1: head.php, spinner.php, navbar.php, footer.php (+ back to top), scripts.php
  partials/                        PART 2: head.php, sidebar.php, header.php, footer.php, scripts.php
  pages/                           page content only: events (+ events-scripts), my-registrations, login, signup,
                                   events-admin, registrations, placeholder
  assets/css/front.css, admin.css, assets/js/main.js
```

## Run
- `php -S localhost:8000` in this folder, then open http://localhost:8000/index.php?page=events
- or XAMPP: copy the folder into `htdocs`, start Apache, open
  http://localhost/paraxis-events/index.php?page=events

## Accounts (no database yet)

| Account | Email | Password | Goes to |
|---|---|---|---|
| Admin | `admin@parasante.demo` | `Admin123!` | `?page=events-admin` (back office) |
| Customer | create one with **Sign up** | 8 characters min. | `?page=events` |

Back-office pages call `requireAdmin()`: anyone else is sent to `?page=login&admin=1`.

## Business rules (unchanged)
- Registering needs a logged-in customer; one active registration per customer and event.
- A registration is refused if the event is finished or full (capacity counts only "registered" rows).
- Cancelling releases the seat; a cancelled registration can be re-activated by registering again.
- An admin cannot set a capacity lower than the seats already taken.
- Re-registering from the back office checks the capacity first.

## Scenario to test
1. Open **Events** → filters Upcoming / Past / All, search box, seats bar on each card.
2. Click **Register** (log in / sign up first) → fill the form → the card shows "✓ You are registered".
3. **My registrations** → cancel a registration → the seat is released.
4. Log in as admin → **Events**: create an event, edit one (try a capacity lower than the seats taken), delete one.
5. **Registrations**: filter by event / status, Cancel / Re-register (the full "Sun protection masterclass" refuses), Delete.

Data is kept in the PHP session (`$_SESSION['events']`, `$_SESSION['event_registrations']`), starting
from the rows written in the models. Close the browser to reset.

## Add a page
1. Create `app/Views/pages/<name>.php` with the page content only.
2. Create `app/Controllers/<Name>Controller.php` extending `Controller` (set `$page`, `$title`, `$layout`)
   and call `$this->render('<name>')`.
3. Add (or point) the route in `app/routes.php`.

## Next step (database)
Only `Models/Model.php` changes: `all()`, `find()`, `where()`, `insert()`, `update()`, `delete()`
will run PDO queries. Controllers and Views stay the same.
Tables: `events(id, title, description, location, event_date, capacity, active, created_at)` and
`event_registrations(id, event_id → events.id, user_id → users.id, participant_name, participant_email, phone, status, created_at)`.
