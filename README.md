# Vehicles API

A small REST API in plain PHP for managing vehicles (create, read, update, delete), with filtering, sorting and validation. Built for the go creations backend developer assessment.

## Progress checklist

### Setup
- [x] Project structure (`public/`, `src/`) with Composer PSR-4 autoloading
- [x] MySQL schema with seed data (`schema.sql`)
- [x] PDO connection (`Database`)
- [x] Front controller and basic routing (`public/index.php`)
- [x] Public GitHub repository created and pushed
- [ ] `go-creations` invited as collaborator

### Vehicle structure
- [x] `id`, `model_name`, `type_id`, `vehicle_type`, `doors`, `transmission`, `fuel`, `price`
- [x] Response format matches the spec (numbers returned as numbers)

### Endpoints
- [x] `GET /vehicles`
- [ ] `POST /vehicles`
- [ ] `PUT /vehicles/{id}`
- [ ] `DELETE /vehicles/{id}`

### Filters
- [ ] Sorting: `sort=name_asc`, `name_desc`, `price_asc`, `price_desc`
- [x] Price filter: `price_min`, `price_max`, or both
- [x] Transmission filter: `manual` / `automatic`
- [x] Type filter: `type_id`
- [x] Combined filters (e.g. `type_id=2&transmission=automatic&price_min=100`)
- [ ] Combined filters with sorting
- [ ] Invalid query parameters return 400

### Validation
- [ ] `model_name` required
- [ ] `type_id` required and numeric
- [ ] `price` number ≥ 0
- [ ] `doors` number > 0
- [ ] `transmission` only `manual` / `automatic`
- [ ] `fuel` only `petrol` / `diesel` / `hybrid` / `electric`

### Error handling
- [x] 404 for unknown routes
- [x] 405 for unsupported methods
- [ ] 404 for unknown vehicle id
- [ ] 422 with all validation errors
- [ ] Global handler: generic 500, details logged

### README
- [ ] How to run
- [ ] Assumptions made
- [ ] What I consider most important

## How to run

Requirements: PHP 8.2+, Composer, MySQL.

```bash
composer dump-autoload
mysql -u <user> -p <database> < schema.sql
php -S localhost:8000 -t public
```

Database settings are local defaults in `src/Database.php`.

## Testing with curl

Start the server with `php -S localhost:8000 -t public`, then run these in a second terminal. Expected results assume a fresh load of `schema.sql`. Add `-i` to any command to see the status code and headers.

### Listing and filters

| Command | Expected |
|---|---|
| `curl "localhost:8000/vehicles"` | 200, all 6 vehicles |
| `curl "localhost:8000/vehicles?price_min=100"` | 200, 5 vehicles (all except Fiat Panda) |
| `curl "localhost:8000/vehicles?price_max=120"` | 200, 2 vehicles: Fiat Panda, Toyota Yaris |
| `curl "localhost:8000/vehicles?price_min=100&price_max=200"` | 200, 4 vehicles: Toyota Yaris, VW Golf, Nissan Qashqai, Ford Transit |
| `curl "localhost:8000/vehicles?transmission=manual"` | 200, 3 vehicles: Fiat Panda, VW Golf, Ford Transit |
| `curl "localhost:8000/vehicles?transmission=automatic"` | 200, 3 vehicles: Toyota Yaris, Nissan Qashqai, Tesla Model Y |
| `curl "localhost:8000/vehicles?type_id=3"` | 200, 2 vehicles: Nissan Qashqai, Tesla Model Y |
| `curl "localhost:8000/vehicles?type_id=3&transmission=automatic&price_min=200"` | 200, 1 vehicle: Tesla Model Y |
| `curl "localhost:8000/vehicles?type_id=99"` | 200, empty list `[]` |

### Routing errors

| Command | Expected |
|---|---|
| `curl -i "localhost:8000/foo"` | 404, `{"error":"Not found"}` |
| `curl -i -X PATCH "localhost:8000/vehicles"` | 405, `{"error":"Method not allowed"}` |

### Still to add (sorting, validation, write endpoints)

| Command | Expected |
|---|---|
| `curl "localhost:8000/vehicles?sort=price_asc"` | 200, cheapest first: Fiat Panda … Tesla Model Y |
| `curl "localhost:8000/vehicles?sort=name_desc"` | 200, VW Golf, Toyota Yaris, Tesla Model Y, Nissan Qashqai, Ford Transit, Fiat Panda |
| `curl -i "localhost:8000/vehicles?sort=banana"` | 400 |
| `curl -i "localhost:8000/vehicles?price_min=abc"` | 400 |
| `curl -i "localhost:8000/vehicles?price_min=200&price_max=100"` | 400 |
| `curl -i "localhost:8000/vehicles?transmission=banana"` | 400 |

Create a vehicle (expected 201 with the new vehicle, including its `id`):

```bash
curl -i -X POST "localhost:8000/vehicles" \
  -H "Content-Type: application/json" \
  -d '{"model_name":"Renault Clio","type_id":1,"vehicle_type":"car","doors":5,"transmission":"manual","fuel":"petrol","price":85}'
```

Invalid body (expected 422 listing every invalid field):

```bash
curl -i -X POST "localhost:8000/vehicles" \
  -H "Content-Type: application/json" \
  -d '{"model_name":"","type_id":"abc","doors":0,"transmission":"semi","fuel":"steam","price":-5}'
```

Malformed JSON (expected 400):

```bash
curl -i -X POST "localhost:8000/vehicles" \
  -H "Content-Type: application/json" \
  -d '{"model_name": '
```

Update a vehicle (expected 200 with the updated vehicle):

```bash
curl -i -X PUT "localhost:8000/vehicles/1" \
  -H "Content-Type: application/json" \
  -d '{"model_name":"Fiat Panda Cross","type_id":2,"vehicle_type":"car","doors":5,"transmission":"manual","fuel":"hybrid","price":95}'
```

| Command | Expected |
|---|---|
| `curl -i -X PUT "localhost:8000/vehicles/999" -H "Content-Type: application/json" -d '{…valid body…}'` | 404 |
| `curl -i -X DELETE "localhost:8000/vehicles/6"` | 204, no body |
| `curl -i -X DELETE "localhost:8000/vehicles/999"` | 404 |

Run `mysql -u <user> -p <database> < schema.sql` to reset the data after testing.

## Assumptions (so far)

- `type_id` references a `vehicle_types` table (category: Economy, Compact, SUV, Van); `vehicle_type` is the kind of vehicle (car, van).
- Price is stored as `DECIMAL(10,2)` and returned as a number.
- Trailing slashes are ignored (`/vehicles/` is the same as `/vehicles`).
