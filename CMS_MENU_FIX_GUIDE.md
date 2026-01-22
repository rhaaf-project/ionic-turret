# Filament CMS Navigation Nesting Guide

This guide documents the solution for correctly nesting resources under a parent menu item in Filament v3, specifically for the **Line** submenu structure.

## The Problem
When trying to nest items (Extension, VPW, CAS, 3rd Party) under the **"Line ▾"** parent menu, some items (like Extension and 3rd Party) were disappearing or not appearing in the correct hierarchy.

## The Solution
To successfully nest a resource under another resource, the child resource must share the **same Navigation Group** as the parent resource.

### Requirements:
1. **Parent Resource** (`LineResource.php`):
   - Defined in a group (e.g., 'Connectivity').
   - Has a label (e.g., 'Line ▾').

2. **Child Resource** (`ExtensionResource.php`, etc.):
   - **MUST** have the same `$navigationGroup` as the parent.
   - **MUST** specify the `$navigationParentItem` matching the parent's navigation label.

### Code Example

**Parent (LineResource.php):**
```php
protected static ?string $navigationGroup = 'Connectivity';
protected static ?string $navigationLabel = 'Line ▾'; // The parent label
```

**Child (ExtensionResource.php):**
```php
// CRITICAL: Must include the group, otherwise it won't nest correctly!
protected static ?string $navigationGroup = 'Connectivity'; 

// Points to the parent's label
protected static ?string $navigationParentItem = 'Line ▾'; 

// Optional: Sort order within the submenu
protected static ?int $navigationSort = 2; 
```

## Summary of Configuration
For the "Line" submenu to work with [Line, Extension, VPW, CAS, 3rd Party], all these resources must have:

| Resource | Navigation Group | Navigation Parent Item | Sort Order |
| :--- | :--- | :--- | :--- |
| **Line** | `'Connectivity'` | `null` (Parent) | `1` |
| **Extension** | `'Connectivity'` | `'Line ▾'` | `2` |
| **VPW** | `'Connectivity'` | `'Line ▾'` | `3` |
| **CAS** | `'Connectivity'` | `'Line ▾'` | `4` |
| **3rd Party** | `'Connectivity'` | `'Line ▾'` | `99` |

**Key Takeaway:** If `$navigationGroup` is missing from the child resource, Filament fails to discover the parent relationship correctly even if `$navigationParentItem` is set.
