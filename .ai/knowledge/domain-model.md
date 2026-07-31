# Domain Model & Entity Definitions

## Central Domain Entities

### `Tenant`
- **Table**: `tenants` (Central DB)
- **Attributes**: `id` (string/uuid), `data` (json), `plan_id` (fk), `trial_ends_at`, `status` (active, suspended, archived), `created_at`, `updated_at`
- **Relationships**: `hasMany(Domain::class)`, `belongsTo(Plan::class)`

### `Domain`
- **Table**: `domains` (Central DB)
- **Attributes**: `id`, `domain` (string, unique), `tenant_id` (fk), `is_primary` (bool), `is_verified` (bool)
- **Relationships**: `belongsTo(Tenant::class)`

### `Plan`
- **Table**: `plans` (Central DB)
- **Attributes**: `id`, `name`, `slug`, `price_monthly`, `price_yearly`, `max_members`, `max_sms_credits`, `features` (json)

---

## Tenant Domain Entities

### `Member`
- **Table**: `members` (Tenant DB)
- **Attributes**: `id`, `first_name`, `last_name`, `email`, `phone`, `gender`, `date_of_birth`, `address`, `town`, `postal_code`, `photo_path`, `status` (active, inactive, deceased), `joined_at`
- **Relationships**: `hasMany(AttendanceMember::class)`, `hasMany(Tithe::class)`, `hasMany(Contribution::class)`

### `Attendance`
- **Table**: `attendances` (Tenant DB)
- **Attributes**: `id`, `title`, `attendance_date` (date), `service_type`, `notes`, `created_by_user_id` (fk)
- **Relationships**: `belongsToMany(Member::class, 'attendance_members')`

### `Tithe`
- **Table**: `tithes` (Tenant DB)
- **Attributes**: `id`, `member_id` (nullable fk), `tithed_on` (date), `amount` (decimal:10,2), `payment_method` (cash, mpesa, card, bank), `reference_no`, `recorded_by_user_id` (fk)
- **Relationships**: `belongsTo(Member::class)`, `belongsTo(User::class, 'recorded_by_user_id')`

### `Offering`
- **Table**: `offerings` (Tenant DB)
- **Attributes**: `id`, `offering_type_id` (fk), `offering_date` (date), `amount` (decimal:10,2), `notes`, `recorded_by_user_id` (fk)
- **Relationships**: `belongsTo(OfferingType::class)`

### `OfferingType`
- **Table**: `offering_types` (Tenant DB)
- **Attributes**: `id`, `name`, `description`, `is_active` (bool)

### `ContributionType`
- **Table**: `contribution_types` (Tenant DB)
- **Attributes**: `id`, `description`, `amount` (decimal), `recurrent` (bool), `recurrence_value` (int), `recurrence_unit` (days, weeks, months), `deadline` (date), `autoenroll` (bool)

### `Contribution`
- **Table**: `contributions` (Tenant DB)
- **Attributes**: `id`, `contribution_type_id` (fk), `member_id` (fk), `amount_due`, `amount_paid`, `balance`, `status` (pending, partial, paid)

### `SmsMessage`
- **Table**: `sms_messages` (Tenant DB)
- **Attributes**: `id`, `message` (text), `cost` (decimal), `status` (draft, queued, sent, failed), `sent_at`
- **Relationships**: `hasMany(SmsRecipient::class)`

### `SmsRecipient`
- **Table**: `sms_recipients` (Tenant DB)
- **Attributes**: `id`, `sms_message_id` (fk), `member_id` (nullable fk), `phone`, `status` (pending, delivered, failed), `error_message`

### `Department`
- **Table**: `departments` (Tenant DB)
- **Attributes**: `id`, `title`, `description`, `leader_member_id` (nullable fk)

### `TenantUser`
- **Table**: `users` (Tenant DB)
- **Attributes**: `id`, `name`, `email`, `password`, `is_admin` (bool), `is_active` (bool), `last_login_at`
- **Relationships**: `roles` via Spatie Permission package
