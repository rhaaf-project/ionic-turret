export type MenuItem = {
  key?: string
  label?: string
  icon?: string
  link?: string
  collapsed?: boolean
  subMenu?: any
  isTitle?: boolean
  badge?: any
  parentKey?: string
  disabled?: boolean
}

export const MENU: MenuItem[] = [
  {
    key: 'general',
    label: 'MAIN',
    isTitle: true,
  },
  {
    key: 'dashboards',
    icon: 'solar:widget-5-bold-duotone',
    label: 'Dashboard',
    link: '/index'
  },
  {
    key: 'extensions',
    icon: 'solar:phone-calling-bold-duotone',
    label: 'Extensions',
    collapsed: true,
    subMenu: [
      {
        key: 'extensions-list',
        label: 'All Extensions',
        link: '/extensions/list',
        parentKey: 'extensions',
      },
      {
        key: 'extensions-create',
        label: 'Add New',
        link: '/extensions/add',
        parentKey: 'extensions',
      }
    ],
  },
  {
    key: 'channels',
    icon: 'solar:tuning-square-bold-duotone',
    label: 'Channels',
    collapsed: true,
    subMenu: [
      {
        key: 'channels-list',
        label: 'Channel Grid',
        link: '/channels/warehouse',
        parentKey: 'channels',
      },
      {
        key: 'channels-config',
        label: 'Configuration',
        link: '/channels/received-order',
        parentKey: 'channels',
      }
    ],
  },
  {
    key: 'call-logs',
    icon: 'solar:phone-rounded-bold-duotone',
    label: 'Call Logs',
    collapsed: true,
    subMenu: [
      {
        key: 'call-logs-list',
        label: 'All Calls',
        link: '/call-logs/list',
        parentKey: 'call-logs',
      },
      {
        key: 'call-logs-detail',
        label: 'Call Details',
        link: '/call-logs/detail',
        parentKey: 'call-logs',
      }
    ],
  },
  {
    key: 'users-section',
    label: 'MANAGEMENT',
    isTitle: true,
  },
  {
    key: 'users',
    icon: 'solar:users-group-two-rounded-bold-duotone',
    label: 'Users',
    collapsed: true,
    subMenu: [
      {
        key: 'users-list',
        label: 'All Users',
        link: '/users/list',
        parentKey: 'users',
      },
      {
        key: 'users-details',
        label: 'User Details',
        link: '/users/detail',
        parentKey: 'users',
      }
    ],
  },
  {
    key: 'roles',
    icon: 'solar:shield-user-bold-duotone',
    label: 'Roles',
    collapsed: true,
    subMenu: [
      {
        key: 'roles-list',
        label: 'All Roles',
        link: '/roles/list',
        parentKey: 'roles',
      },
      {
        key: 'roles-create',
        label: 'Add Role',
        link: '/roles/add',
        parentKey: 'roles',
      }
    ],
  },
  {
    key: 'settings-section',
    label: 'SETTINGS',
    isTitle: true,
  },
  {
    key: 'settings',
    icon: 'solar:settings-bold-duotone',
    label: 'Settings',
    link: '/pages/settings',
  },
];
