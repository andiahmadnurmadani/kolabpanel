import { Framework, TerminalAction, FileNode } from "./types";

export const FRAMEWORK_ICONS: Record<Framework, string> = {
  [Framework.LARAVEL]: 'text-red-600',
  [Framework.NEXTJS]: 'text-black',
  [Framework.REACT]: 'text-blue-500',
  [Framework.NODEJS]: 'text-green-600',
  [Framework.PHP]: 'text-indigo-600',
  [Framework.HTML]: 'text-orange-600',
};

// Helper to generate mock files based on framework (Still needed for File Manager simulation on frontend)
export const getMockFiles = (framework: Framework): FileNode[] => {
  const common: FileNode[] = [
    { id: 'f_env', name: '.env', type: 'file', size: '1 KB', path: '/', createdAt: '2023-10-15' },
    { id: 'f_readme', name: 'README.md', type: 'file', size: '2 KB', path: '/', createdAt: '2023-10-15' },
  ];

  switch (framework) {
    case Framework.NEXTJS:
    case Framework.REACT:
      return [
        { id: 'd_node', name: 'node_modules', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_public', name: 'public', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_src', name: 'src', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'f_pkg', name: 'package.json', type: 'file', size: '2 KB', path: '/', createdAt: '2023-10-15' },
        { id: 'f_cfg', name: 'next.config.js', type: 'file', size: '1 KB', path: '/', createdAt: '2023-10-15' },
        // Add some nested files for demo
        { id: 'f_index', name: 'index.tsx', type: 'file', size: '2 KB', path: '/src', createdAt: '2023-10-15' },
        { id: 'f_app', name: 'App.tsx', type: 'file', size: '4 KB', path: '/src', createdAt: '2023-10-15' },
        { id: 'f_comp', name: 'components', type: 'folder', size: '-', path: '/src', createdAt: '2023-10-15' },
        ...common
      ];
    case Framework.LARAVEL:
      return [
        { id: 'd_app', name: 'app', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_boot', name: 'bootstrap', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_cfg', name: 'config', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_pub', name: 'public', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'f_art', name: 'artisan', type: 'file', size: '4 KB', path: '/', createdAt: '2023-10-15' },
        { id: 'f_comp', name: 'composer.json', type: 'file', size: '2 KB', path: '/', createdAt: '2023-10-15' },
        ...common
      ];
    case Framework.NODEJS:
      return [
        { id: 'd_node', name: 'node_modules', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'd_src', name: 'src', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'f_idx', name: 'index.js', type: 'file', size: '4 KB', path: '/', createdAt: '2023-10-15' },
        { id: 'f_pkg', name: 'package.json', type: 'file', size: '2 KB', path: '/', createdAt: '2023-10-15' },
        ...common
      ];
    default:
      return [
        { id: 'd_assets', name: 'assets', type: 'folder', size: '-', path: '/', createdAt: '2023-10-15' },
        { id: 'f_idx', name: 'index.html', type: 'file', size: '5 KB', path: '/', createdAt: '2023-10-15' },
        { id: 'f_css', name: 'style.css', type: 'file', size: '3 KB', path: '/', createdAt: '2023-10-15' },
        ...common
      ];
  }
};

export const SAFE_COMMANDS: Record<string, TerminalAction[]> = {
  [Framework.LARAVEL]: [
    { id: 'migrate', label: 'Run Migrations', command: 'php artisan migrate --force', description: 'Run database migrations', isDangerous: true },
    { id: 'seed', label: 'Seed Database', command: 'php artisan db:seed --force', description: 'Populate database with seed data', isDangerous: true },
    { id: 'cache_clear', label: 'Clear Cache', command: 'php artisan cache:clear', description: 'Flush the application cache' },
    { id: 'optimize', label: 'Optimize', command: 'php artisan optimize', description: 'Cache framework bootstrap files' },
    { id: 'view_clear', label: 'Clear Views', command: 'php artisan view:clear', description: 'Clear compiled view files' },
  ],
  [Framework.NODEJS]: [
    { id: 'npm_install', label: 'Install Dependencies', command: 'npm install --production', description: 'Install packages from package.json' },
    { id: 'npm_build', label: 'Build Project', command: 'npm run build', description: 'Compile the application' },
    { id: 'npm_start', label: 'Start Application', command: 'npm start', description: 'Start the Node.js server' },
  ],
  [Framework.NEXTJS]: [
    { id: 'npm_install', label: 'Install Dependencies', command: 'npm install --production', description: 'Install packages from package.json' },
    { id: 'npm_build', label: 'Build Project', command: 'npm run build', description: 'Build Next.js application' },
    { id: 'npm_start', label: 'Start Server', command: 'npm start', description: 'Start the production server' },
  ],
  [Framework.REACT]: [
    { id: 'npm_install', label: 'Install Dependencies', command: 'npm install', description: 'Install packages from package.json' },
    { id: 'npm_build', label: 'Build Project', command: 'npm run build', description: 'Build React application for production' },
    { id: 'npm_start', label: 'Start Dev Server', command: 'npm start', description: 'Start development server' },
  ]
};

export const GENERIC_ACTIONS: TerminalAction[] = [];