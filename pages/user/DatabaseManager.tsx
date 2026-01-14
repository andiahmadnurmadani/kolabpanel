import React, { useState } from 'react';
import { StatusBadge } from '../../components/Shared';
import { User, Site } from '../../types';
import { Database, User as UserIcon, Key, Eye, EyeOff, Server, ExternalLink, Trash2, Copy, Shield, Globe, Terminal, AlertCircle, Link, Table, ChevronDown, ChevronUp, FileSpreadsheet, HardDrive } from 'lucide-react';

interface DatabaseManagerProps {
  sites: Site[];
  user: User;
}

interface MockTable {
    name: string;
    rows: number;
    size: string;
    engine: string;
    collation: string;
}

export const DatabaseManager: React.FC<DatabaseManagerProps> = ({ sites, user }) => {
    // Top-level safety checks
    if (!user || !user.username) {
        return <div className="p-10 text-center text-slate-500">Loading profile data...</div>;
    }

    const safeSites = Array.isArray(sites) ? sites : [];
    // Only show databases for sites that requested one
    const dbSites = safeSites.filter(site => site.hasDatabase);
    
    // MASTER CREDENTIALS GENERATION
    const masterDbUser = `sql_${user.username.toLowerCase().replace(/[^a-z0-9]/g, '')}`;
    const masterDbPass = `kp_${user.id.substring(0,4)}@${user.username.substring(0,3).toUpperCase()}#88`;
    const masterHost = "127.0.0.1";
    const masterPort = "3306";
    
    const [showMasterPass, setShowMasterPass] = useState(false);
    const [expandedDb, setExpandedDb] = useState<string | null>(null);

    const copyToClipboard = (text: string) => {
        if (navigator && navigator.clipboard) {
            navigator.clipboard.writeText(text);
        } else {
             const textarea = document.createElement("textarea");
             textarea.value = text;
             document.body.appendChild(textarea);
             textarea.select();
             try {
                 document.execCommand('copy');
             } catch (err) {
                 console.error('Failed to copy', err);
             }
             document.body.removeChild(textarea);
        }
    };

    // Helper to generate mock tables based on framework type
    const getMockTables = (site: Site): MockTable[] => {
        const common = [
            { name: 'migrations', rows: 12, size: '16 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
            { name: 'failed_jobs', rows: 0, size: '16 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
            { name: 'users', rows: Math.floor(Math.random() * 50) + 1, size: '48 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
        ];

        if (site.framework.includes('Laravel') || site.framework.includes('PHP')) {
            return [
                ...common,
                { name: 'password_resets', rows: 0, size: '16 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
                { name: 'personal_access_tokens', rows: 5, size: '32 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
            ];
        } else {
            // JS frameworks often allow arbitrary schemas, add generic ones
            return [
                ...common,
                { name: 'posts', rows: 124, size: '256 KB', engine: 'InnoDB', collation: 'utf8mb4_general_ci' },
                { name: 'comments', rows: 450, size: '512 KB', engine: 'InnoDB', collation: 'utf8mb4_general_ci' },
                { name: 'settings', rows: 15, size: '16 KB', engine: 'MyISAM', collation: 'utf8mb4_general_ci' },
            ];
        }
    };

    const toggleDbExpansion = (id: string) => {
        setExpandedDb(prev => prev === id ? null : id);
    };

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            {/* 1. Header & Server Status */}
            <div className="bg-white rounded-xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
                <div className="flex items-center gap-5">
                    <div className="p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                        <Server className="w-8 h-8 text-indigo-600" />
                    </div>
                    <div>
                        <h2 className="text-xl font-bold text-slate-900 mb-1">MySQL Database Server</h2>
                        <div className="flex items-center gap-4 text-sm text-slate-500">
                            <span className="flex items-center gap-1.5"><div className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div> Service Online</span>
                            <span>•</span>
                            <span>Ver 8.0</span>
                            <span>•</span>
                            <span className="text-indigo-600 font-medium">{masterDbUser}</span>
                        </div>
                    </div>
                </div>

                <div className="flex gap-3">
                     <button className="py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                        <ExternalLink className="w-4 h-4" /> Open phpMyAdmin
                    </button>
                </div>
            </div>

            {/* 2. MASTER CREDENTIALS CARD (Always Visible) */}
            <div className="bg-slate-900 rounded-2xl p-1 shadow-xl overflow-hidden">
                <div className="bg-slate-800/50 p-4 border-b border-slate-700/50 flex items-center justify-between">
                     <h3 className="text-white font-bold flex items-center gap-2">
                        <Key className="w-5 h-5 text-emerald-400" />
                        Master Access Credentials
                     </h3>
                     <span className="text-xs text-slate-400 bg-slate-900 px-3 py-1 rounded-full border border-slate-700">
                        Use these details to connect to ALL your databases
                     </span>
                </div>
                
                <div className="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-10 relative">
                     {/* Decoration */}
                     <div className="absolute right-0 bottom-0 opacity-5 pointer-events-none">
                        <Terminal className="w-64 h-64 text-white" />
                     </div>

                     {/* Left: Connection Info */}
                     <div className="space-y-6 relative z-10">
                        <div className="grid grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-[10px] uppercase tracking-wider font-bold text-slate-400 flex items-center gap-1">
                                    <Globe className="w-3 h-3" /> Hostname
                                </label>
                                <div className="flex items-center gap-2 group cursor-pointer" onClick={() => copyToClipboard(masterHost)}>
                                    <code className="text-xl font-mono font-bold text-emerald-400 group-hover:text-emerald-300 transition-colors">{masterHost}</code>
                                    <Copy className="w-4 h-4 text-slate-600 group-hover:text-emerald-400 opacity-0 group-hover:opacity-100 transition-all" />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <label className="text-[10px] uppercase tracking-wider font-bold text-slate-400 flex items-center gap-1">
                                    <Server className="w-3 h-3" /> Port
                                </label>
                                <div className="flex items-center gap-2 group cursor-pointer" onClick={() => copyToClipboard(masterPort)}>
                                    <code className="text-xl font-mono font-bold text-emerald-400 group-hover:text-emerald-300 transition-colors">{masterPort}</code>
                                    <Copy className="w-4 h-4 text-slate-600 group-hover:text-emerald-400 opacity-0 group-hover:opacity-100 transition-all" />
                                </div>
                            </div>
                        </div>
                        
                        <div className="p-4 bg-indigo-500/10 border border-indigo-500/20 rounded-xl">
                            <h4 className="text-indigo-300 text-xs font-bold mb-1 flex items-center gap-1"><AlertCircle className="w-3 h-3" /> Important</h4>
                            <p className="text-indigo-200/70 text-xs leading-relaxed">
                                This user has full privileges (SELECT, INSERT, UPDATE, DELETE) on all databases created under your account. Do not share these credentials.
                            </p>
                        </div>
                     </div>

                     {/* Right: Auth Info */}
                     <div className="space-y-5 relative z-10">
                        <div className="space-y-2">
                            <label className="text-[10px] uppercase tracking-wider font-bold text-slate-400 flex items-center gap-1">
                                <UserIcon className="w-3 h-3" /> Master Username
                            </label>
                            <div className="flex items-center bg-slate-950 rounded-lg border border-slate-700 focus-within:border-indigo-500 transition-colors">
                                <input 
                                    type="text" 
                                    readOnly 
                                    value={masterDbUser} 
                                    className="bg-transparent border-none text-sm font-mono text-slate-200 w-full focus:ring-0 px-4 py-3"
                                />
                                <button 
                                    onClick={() => copyToClipboard(masterDbUser)} 
                                    className="p-3 text-slate-500 hover:text-white hover:bg-slate-800 rounded-r-lg transition-colors border-l border-slate-800"
                                >
                                    <Copy className="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <label className="text-[10px] uppercase tracking-wider font-bold text-slate-400 flex items-center gap-1">
                                <Key className="w-3 h-3" /> Master Password
                            </label>
                            <div className="flex items-center bg-slate-950 rounded-lg border border-slate-700 focus-within:border-indigo-500 transition-colors">
                                <input 
                                    type={showMasterPass ? "text" : "password"} 
                                    readOnly 
                                    value={masterDbPass} 
                                    className="bg-transparent border-none text-sm font-mono text-emerald-400 w-full focus:ring-0 px-4 py-3"
                                />
                                <button 
                                    onClick={() => setShowMasterPass(!showMasterPass)} 
                                    className="p-3 text-slate-500 hover:text-white hover:bg-slate-800 transition-colors border-l border-r border-slate-800"
                                >
                                    {showMasterPass ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                                </button>
                                <button 
                                    onClick={() => copyToClipboard(masterDbPass)} 
                                    className="p-3 text-slate-500 hover:text-white hover:bg-slate-800 rounded-r-lg transition-colors"
                                >
                                    <Copy className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                     </div>
                </div>
            </div>

            {/* 3. Databases List */}
            <div className="space-y-4">
                 <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2 px-1">
                    <Database className="w-5 h-5 text-indigo-600" />
                    Your Databases
                    <span className="text-xs font-normal text-slate-500 bg-slate-100 px-2 py-1 rounded-full">{dbSites.length} Active</span>
                 </h3>

                 {dbSites.length > 0 ? (
                    <div className="flex flex-col gap-4">
                    {dbSites.map((site, idx) => {
                        const dbName = `db_${site.subdomain.replace(/[^a-z0-9]/g, '')}`;
                        const isExpanded = expandedDb === site.id;
                        const tables = getMockTables(site);
                        const totalRows = tables.reduce((acc, t) => acc + t.rows, 0);

                        return (
                            <div key={site.id} className={`bg-white rounded-xl shadow-sm border transition-all duration-300 overflow-hidden ${isExpanded ? 'border-indigo-200 ring-2 ring-indigo-50 shadow-md' : 'border-slate-200 hover:border-indigo-300'}`}>
                                {/* Header / Summary Row */}
                                <div 
                                    className="p-5 flex flex-col md:flex-row justify-between items-center gap-4 cursor-pointer bg-slate-50/30 hover:bg-slate-50 transition-colors"
                                    onClick={() => toggleDbExpansion(site.id)}
                                >
                                    <div className="flex items-center gap-4 w-full md:w-auto">
                                        <div className={`w-12 h-12 rounded-lg flex items-center justify-center border shadow-sm transition-colors ${isExpanded ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-600 border-slate-200'}`}>
                                            <Database className="w-6 h-6" />
                                        </div>
                                        <div>
                                            <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Database Name</div>
                                            <h4 className="font-bold text-slate-900 font-mono text-lg">{dbName}</h4>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                        <div className="hidden md:block text-right">
                                            <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Linked Project</div>
                                            <div className="font-medium text-slate-700 flex items-center gap-1 justify-end">
                                                <Link className="w-3 h-3" /> {site.name}
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-center gap-3">
                                            <div className="hidden sm:flex flex-col items-end mr-2">
                                                 <span className="text-xs font-bold text-slate-700">{tables.length} Tables</span>
                                                 <span className="text-[10px] text-slate-500">~{totalRows} Rows</span>
                                            </div>
                                            <button 
                                                className={`p-2 rounded-lg transition-colors border ${isExpanded ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-white text-slate-400 border-slate-200'}`}
                                            >
                                                {isExpanded ? <ChevronUp className="w-5 h-5" /> : <ChevronDown className="w-5 h-5" />}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                {/* Expanded Content: Table List */}
                                {isExpanded && (
                                    <div className="border-t border-slate-200 animate-in slide-in-from-top-2 duration-200">
                                        <div className="p-6 bg-slate-50/50">
                                            <div className="flex items-center justify-between mb-4">
                                                <h5 className="font-bold text-slate-800 flex items-center gap-2">
                                                    <Table className="w-4 h-4 text-slate-500" />
                                                    Database Tables
                                                </h5>
                                                <div className="text-xs text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">
                                                    Showing {tables.length} tables
                                                </div>
                                            </div>
                                            
                                            <div className="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                                                <div className="overflow-x-auto">
                                                    <table className="min-w-full text-left text-sm">
                                                        <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
                                                            <tr>
                                                                <th className="px-4 py-3 font-medium">Table Name</th>
                                                                <th className="px-4 py-3 font-medium">Rows</th>
                                                                <th className="px-4 py-3 font-medium">Size</th>
                                                                <th className="px-4 py-3 font-medium">Engine</th>
                                                                <th className="px-4 py-3 font-medium">Collation</th>
                                                                <th className="px-4 py-3 font-medium text-right">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody className="divide-y divide-slate-100">
                                                            {tables.map((t, i) => (
                                                                <tr key={i} className="hover:bg-indigo-50/30 transition-colors group">
                                                                    <td className="px-4 py-3 font-medium text-slate-700 flex items-center gap-2">
                                                                        <FileSpreadsheet className="w-4 h-4 text-slate-400 group-hover:text-indigo-500" />
                                                                        {t.name}
                                                                    </td>
                                                                    <td className="px-4 py-3 text-slate-600">{t.rows.toLocaleString()}</td>
                                                                    <td className="px-4 py-3 text-slate-600 font-mono text-xs">{t.size}</td>
                                                                    <td className="px-4 py-3 text-slate-500 text-xs">{t.engine}</td>
                                                                    <td className="px-4 py-3 text-slate-500 text-xs">{t.collation}</td>
                                                                    <td className="px-4 py-3 text-right">
                                                                        <div className="flex justify-end gap-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                                                            <button className="text-xs text-indigo-600 hover:underline">Browse</button>
                                                                            <span className="text-slate-300">|</span>
                                                                            <button className="text-xs text-indigo-600 hover:underline">Structure</button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div className="mt-4 flex justify-end">
                                                <button className="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded transition-colors flex items-center gap-1">
                                                    <Trash2 className="w-3 h-3" /> Drop Database
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        );
                    })}
                    </div>
                 ) : (
                    <div className="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center">
                        <div className="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                           <Database className="w-8 h-8" />
                        </div>
                        <h3 className="text-lg font-bold text-slate-800">No Databases Found</h3>
                        <p className="text-slate-500 mt-2 max-w-sm mx-auto text-sm">
                            You haven't created any databases yet. To create one, deploy a new site and check the "Create MySQL Database" option.
                        </p>
                    </div>
                 )}
            </div>
        </div>
    );
};
