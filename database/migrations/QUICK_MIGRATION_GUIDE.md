# 🚀 Quick Migration Guide

## Before Running Migration

### 1. Backup Database ⚠️

```bash
# MySQL/MariaDB
mysqldump -u username -p gerobaks_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Or use Laravel
php artisan db:backup
```

### 2. Check Safety

```bash
cd backend

# Run safety checker
php database/migrations/check_migration_safety.php

# Or manually check
php artisan migrate:status
php artisan db:show
```

### 3. Dry Run

```bash
# See what will happen WITHOUT actually running
php artisan migrate --pretend
```

---

## Running Migration

### Development

```bash
cd backend
php artisan migrate
```

### Production

```bash
cd backend

# 1. Enable maintenance mode
php artisan down

# 2. Run migration
php artisan migrate --force

# 3. Verify
php artisan db:table schedules

# 4. Disable maintenance mode
php artisan up
```

---

## Verify Success

### Check Table Structure

```bash
# Using Laravel
php artisan db:table schedules

# Or MySQL directly
mysql -u username -p -e "DESCRIBE gerobaks_db.schedules"
```

### Expected New Columns

```
waste_items              | json        | YES  | NULL
total_estimated_weight   | decimal(8,2)| NO   | 0.00
```

### Check Index

```sql
SHOW INDEXES FROM schedules WHERE Key_name = 'total_estimated_weight';
```

---

## Test Migration

### Insert Test Data

```sql
INSERT INTO schedules (
    title,
    description,
    latitude,
    longitude,
    pickup_latitude,
    pickup_longitude,
    waste_items,
    total_estimated_weight,
    status
) VALUES (
    'Test Multiple Waste',
    'Test pickup',
    -6.2000,
    106.8167,
    -6.2000,
    106.8167,
    '[{"waste_type":"organik","estimated_weight":5.5,"unit":"kg"},{"waste_type":"plastik","estimated_weight":2.0,"unit":"kg"}]',
    7.50,
    'pending'
);
```

### Query Test

```sql
-- Get schedules with waste items
SELECT id, title, waste_items, total_estimated_weight
FROM schedules
WHERE waste_items IS NOT NULL;

-- Get schedules with weight > 5kg
SELECT * FROM schedules WHERE total_estimated_weight > 5.00;
```

---

## Rollback (If Needed)

### Rollback Last Migration

```bash
php artisan migrate:rollback
```

### Rollback Specific Steps

```bash
# Rollback last migration only
php artisan migrate:rollback --step=1

# Rollback multiple
php artisan migrate:rollback --step=3
```

### Verify Rollback

```bash
# Check columns removed
php artisan db:table schedules

# Should NOT see waste_items or total_estimated_weight
```

---

## Troubleshooting

### Error: Column already exists

```
Migration already applied. Check:
php artisan migrate:status
```

### Error: Table doesn't exist

```
Run previous migrations first:
php artisan migrate
```

### Error: JSON not supported

```
Upgrade database:
- MySQL 5.7+ required
- MariaDB 10.2+ required

Check version:
mysql -V
```

### Error: Permission denied

```
Grant ALTER permission:
GRANT ALTER ON gerobaks_db.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

---

## Production Checklist

-   [ ] Database backup created
-   [ ] Safety checker passed
-   [ ] Dry run completed
-   [ ] Team notified
-   [ ] Maintenance mode enabled
-   [ ] Migration executed
-   [ ] Table structure verified
-   [ ] Test data inserted
-   [ ] API tested
-   [ ] Frontend tested
-   [ ] Monitoring active
-   [ ] Maintenance mode disabled
-   [ ] Deployment log updated

---

## Commands Reference

```bash
# Status
php artisan migrate:status        # List all migrations
php artisan migrate:rollback      # Undo last batch
php artisan migrate:fresh         # Drop all & re-migrate (DEV ONLY!)
php artisan migrate:refresh       # Rollback all & re-migrate (DEV ONLY!)

# Info
php artisan db:show               # Show database info
php artisan db:table schedules    # Show table structure
php artisan db:monitor            # Monitor queries

# Safety
php artisan migrate --pretend     # Dry run
php artisan down                  # Maintenance mode
php artisan up                    # Exit maintenance
```

---

## Need Help?

1. Check `MIGRATION_SAFETY_VERIFICATION.md` for full docs
2. Run safety checker: `php database/migrations/check_migration_safety.php`
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test rollback in development first

**Status**: ✅ Migration is safe and ready to run!
