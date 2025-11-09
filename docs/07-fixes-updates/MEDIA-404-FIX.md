# Fix: Media 404 Errors in WordPress Editor

**Date**: November 8, 2025  
**Priority**: Medium  
**Status**: ✅ Fixed

## 🐛 Problem

Console errors appeared when editing posts in the WordPress/Gutenberg editor:

```
GET http://localhost/wordpress/wp-json/wp/v2/media/334?context=view&_locale=user 404 (Not Found)
GET http://localhost/wordpress/wp-json/wp/v2/media/335?context=view&_locale=user 404 (Not Found)
```

### Root Cause

1. **Missing Attachments**: Posts referenced media attachments (IDs 334, 335) that were deleted or never created
2. **No Featured Image Auto-Setting**: WPForms uploads were processed but never set as featured images
3. **No Cleanup on Deletion**: When attachments were deleted, post metadata (`_thumbnail_id`) wasn't cleaned up

## ✅ Solution

### 1. Auto-Set Featured Images

**File**: `inc/wpforms-integration.php`

Modified `archi_process_uploaded_files()` to automatically set the first uploaded image as the post's featured image:

```php
function archi_process_uploaded_files($fields, $post_id, $field_ids) {
    $first_image_set = false;
    
    foreach ($field_ids as $field_id) {
        if (!empty($fields[$field_id]['value'])) {
            $files = is_array($fields[$field_id]['value']) ? $fields[$field_id]['value'] : [$fields[$field_id]['value']];
            
            foreach ($files as $file_url) {
                if (!empty($file_url)) {
                    $attachment_id = archi_attach_uploaded_file($file_url, $post_id);
                    
                    // ✨ NEW: Set first image as featured
                    if ($attachment_id && !$first_image_set && !has_post_thumbnail($post_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                        $first_image_set = true;
                    }
                }
            }
        }
    }
}
```

**Benefits**:
- Automatically assigns featured images to form submissions
- Only sets if no featured image exists
- Uses the first successfully uploaded image

### 2. Cleanup Deleted Attachments

**File**: `inc/wpforms-integration.php`

Added `archi_cleanup_deleted_attachment()` to remove broken references when media is deleted:

```php
function archi_cleanup_deleted_attachment($attachment_id) {
    // Check if this was a featured image for any post
    $posts_with_thumbnail = get_posts([
        'post_type' => ['post', 'archi_project', 'archi_illustration'],
        'post_status' => 'any',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_thumbnail_id',
                'value' => $attachment_id,
                'compare' => '='
            ]
        ]
    ]);
    
    foreach ($posts_with_thumbnail as $post) {
        delete_post_meta($post->ID, '_thumbnail_id');
    }
}
add_action('delete_attachment', 'archi_cleanup_deleted_attachment');
```

**Benefits**:
- Automatically cleans up broken references
- Prevents future 404 errors
- Works retroactively when attachments are deleted

### 3. Maintenance Tool

**File**: `utilities/maintenance/cleanup-broken-media-references.php`

Created web-based utility to:
- ✅ Scan all posts for broken media references
- ✅ Display detailed report of issues
- ✅ One-click cleanup of broken references
- ✅ Admin-only access with security checks

**Access**: `/wp-content/themes/archi-graph-template/utilities/maintenance/cleanup-broken-media-references.php`

## 🔧 How to Fix Existing Issues

### Method 1: Web Interface (Recommended)

1. Login as admin
2. Navigate to: `http://yoursite.local/wp-content/themes/archi-graph-template/utilities/maintenance/cleanup-broken-media-references.php`
3. Review detected broken references
4. Click "Nettoyer les références cassées"

### Method 2: WP-CLI

```bash
cd /path/to/wordpress
wp eval-file wp-content/themes/archi-graph-template/utilities/maintenance/cleanup-broken-media-references.php
```

### Method 3: Manual SQL Query

```sql
-- Find posts with broken thumbnail references
SELECT p.ID, p.post_title, pm.meta_value as attachment_id
FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE pm.meta_key = '_thumbnail_id'
AND pm.meta_value NOT IN (
    SELECT ID FROM wp_posts WHERE post_type = 'attachment'
);

-- Delete broken references (after verification!)
DELETE pm FROM wp_postmeta pm
LEFT JOIN wp_posts p ON pm.meta_value = p.ID
WHERE pm.meta_key = '_thumbnail_id'
AND (p.ID IS NULL OR p.post_type != 'attachment');
```

## 📊 Impact

### Before Fix
- ❌ Console errors on every page edit
- ❌ Broken featured image references
- ❌ No automatic featured image setting
- ❌ Manual cleanup required

### After Fix
- ✅ No console errors
- ✅ Automatic featured image assignment
- ✅ Automatic cleanup on deletion
- ✅ Maintenance tool for existing issues

## 🧪 Testing

### Test Case 1: New Form Submission
1. Submit a project via WPForms with images
2. Verify first image is set as featured
3. Check no 404 errors in console

### Test Case 2: Delete Attachment
1. Delete an attachment used as featured image
2. Edit the post that used it
3. Verify no 404 errors

### Test Case 3: Cleanup Tool
1. Access cleanup utility
2. Verify it detects broken references
3. Run cleanup
4. Verify references are removed

## 📝 Related Files

- `inc/wpforms-integration.php` - Main form processing & fixes
- `utilities/maintenance/cleanup-broken-media-references.php` - Cleanup tool
- `utilities/README.md` - Documentation updated

## 🔮 Future Improvements

1. **Extended Cleanup**: Also check for broken references in:
   - Gallery blocks
   - Custom meta fields
   - Post content image blocks

2. **Scheduled Task**: Add WP-Cron job to periodically scan and clean

3. **Admin Notice**: Notify admins when broken references are detected

4. **Prevention**: Add validation before setting attachment references

## ⚠️ Notes

- This fix is **backward compatible**
- Existing posts with valid attachments are unaffected
- Only broken references are removed
- No featured images are changed if they exist and are valid

## 🔗 References

- WordPress Codex: [`set_post_thumbnail()`](https://developer.wordpress.org/reference/functions/set_post_thumbnail/)
- WordPress Codex: [`delete_attachment` hook](https://developer.wordpress.org/reference/hooks/delete_attachment/)
- WPForms: [Form Processing Hooks](https://wpforms.com/developers/wpforms_process_complete/)

---

**Author**: AI Assistant  
**Tested**: ✅ Local Development  
**Ready for Production**: ✅ Yes
