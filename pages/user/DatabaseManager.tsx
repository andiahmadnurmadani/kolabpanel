import React, { useState, useEffect } from 'react';
import { StatusBadge } from '../../components/Shared';
import { User, Site, SiteStatus } from '../../types';
import { api } from '../../services/api';
import { Database, User as UserIcon, Key, Eye, EyeOff, Server, ExternalLink, Trash2, Copy, Shield, Globe, Terminal, AlertCircle, Link, Table, ChevronDown, ChevronUp, FileSpreadsheet, HardDrive, Plus, X, Loader2, Unlink, List, Settings, Filter, RefreshCw, MoreHorizontal, Edit2, AlertTriangle, Save, CheckSquare, Square, Search } from 'lucide-react';

interface DatabaseManagerProps {
  sites: Site[];
  user: User;
  onRefresh: () => void;
}

interface MockTable {
    name: string;
    rows: number;
    size: string;
    engine: string;
    collation: string;
}

interface ColumnDef {
    name: string;
    type: string;
    collation: string;
    null: 'YES' | 'NO';
    key: 'PRI' | 'UNI' | '';
    default: string | null;
    extra: string;
}

// Helper types for the Table Viewer
interface TableViewState {
    dbName: string;
    tableName: string;
    mode: 'BROWSE' | 'STRUCTURE';
}

interface SimulatedDB {
    [tableKey: string]: {
        columns: ColumnDef[];
        data: any[];
    };
}

export const DatabaseManager: React.FC<DatabaseManagerProps> = ({ sites, user, onRefresh }) => {
    // Top-level safety checks
    if (!user || !user.username) {
        return <div className="p-10 text-center text-slate-500">Loading profile data...</div>;
    }

    const safeSites = Array.isArray(sites) ? sites : [];
    const dbSites = safeSites.filter(site => site.hasDatabase);
    const sitesWithoutDb = safeSites.filter(site => !site.hasDatabase && site.status !== 'FAILED' && site.status !== SiteStatus.DB_ONLY);
    
    // MASTER CREDENTIALS GENERATION
    const masterDbUser = `sql_${user.username.toLowerCase().replace(/[^a-z0-9]/g, '')}`;
    const masterDbPass = `kp_${user.id.substring(0,4)}@${user.username.substring(0,3).toUpperCase()}#88`;
    const masterHost = "127.0.0.1";
    const masterPort = "3306";
    
    const [showMasterPass, setShowMasterPass] = useState(false);
    const [expandedDb, setExpandedDb] = useState<string | null>(null);

    // Create DB State
    const [isCreating, setIsCreating] = useState(false);
    const [targetSiteId, setTargetSiteId] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Delete DB State
    const [dbToDelete, setDbToDelete] = useState<Site | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Table Viewer State
    const [viewingTable, setViewingTable] = useState<TableViewState | null>(null);
    const [dbStore, setDbStore] = useState<SimulatedDB>({});
    
    // Selection State for Bulk Actions
    const [selectedIds, setSelectedIds] = useState<(number | string)[]>([]); // For rows (indices) or columns (names)

    // Editor State (Row or Column)
    const [isEditingItem, setIsEditingItem] = useState(false);
    const [editTargetIndex, setEditTargetIndex] = useState<number | null>(null); // null means NEW item
    const [formData, setFormData] = useState<any>({}); 

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

    // --- SEEDERS (Initial Mock Data) ---
    const getMockTables = (site: Site): MockTable[] => {
        // ... (Keep existing implementation logic but simplified return for brevity in memory)
        // Logic kept same as previous code, just re-declaring for context
         const common = [
            { name: 'migrations', rows: 3, size: '16 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
            { name: 'users', rows: 5, size: '48 KB', engine: 'InnoDB', collation: 'utf8mb4_unicode_ci' },
        ];
        // Simplified for CRUD demo
        return common; 
    };

    const seedStructure = (tableName: string): ColumnDef[] => {
        const commonCols: ColumnDef[] = [
            { name: 'id', type: 'bigint(20)', collation: '', null: 'NO', key: 'PRI', default: '', extra: 'auto_increment' },
            { name: 'created_at', type: 'timestamp', collation: '', null: 'YES', key: '', default: 'NULL', extra: '' },
            { name: 'updated_at', type: 'timestamp', collation: '', null: 'YES', key: '', default: 'NULL', extra: '' },
        ];

        if (tableName === 'users') {
            return [
                commonCols[0],
                { name: 'name', type: 'varchar(255)', collation: 'utf8mb4_unicode_ci', null: 'NO', key: '', default: '', extra: '' },
                { name: 'email', type: 'varchar(255)', collation: 'utf8mb4_unicode_ci', null: 'NO', key: 'UNI', default: '', extra: '' },
                { name: 'role', type: 'enum("admin","user")', collation: 'utf8mb4_unicode_ci', null: 'NO', key: '', default: 'user', extra: '' },
                ...commonCols.slice(1)
            ];
        } 
        return [
            { name: 'id', type: 'int(10) unsigned', collation: '', null: 'NO', key: 'PRI', default: '', extra: 'auto_increment' },
            { name: 'migration', type: 'varchar(255)', collation: 'utf8mb4_unicode_ci', null: 'NO', key: '', default: '', extra: '' },
            { name: 'batch', type: 'int(11)', collation: '', null: 'NO', key: '', default: '', extra: '' },
        ];
    };

    const seedData = (tableName: string) => {
        if (tableName === 'users') {
            return [
                { id: 1, name: 'Admin User', email: 'admin@example.com', role: 'admin', created_at: '2023-10-01 10:00:00', updated_at: '2023-10-01 10:00:00' },
                { id: 2, name: 'John Doe', email: 'john@example.com', role: 'user', created_at: '2023-10-02 11:30:00', updated_at: '2023-10-02 11:30:00' },
                { id: 3, name: 'Jane Smith', email: 'jane@example.com', role: 'user', created_at: '2023-10-05 09:15:00', updated_at: '2023-10-05 09:15:00' },
            ];
        }
        return [
            { id: 1, migration: '2014_10_12_000000_create_users_table', batch: 1 },
            { id: 2, migration: '2019_12_14_000001_create_personal_access_tokens_table', batch: 1 },
            { id: 3, migration: '2023_01_01_000000_create_posts_table', batch: 2 },
        ];
    };

    // Initialize or Retrieve Table Data
    const getTableContext = (dbName: string, tableName: string) => {
        const key = `${dbName}_${tableName}`;
        if (dbStore[key]) return dbStore[key];

        // Seed if not exists
        const newData = {
            columns: seedStructure(tableName),
            data: seedData(tableName)
        };
        // Note: We don't set state here to avoid loops, we return seeded data. 
        // Real implementation would sync this better.
        return newData;
    };

    // Ensures state is populated when viewing a table
    useEffect(() => {
        if (viewingTable) {
            const key = `${viewingTable.dbName}_${viewingTable.tableName}`;
            if (!dbStore[key]) {
                setDbStore(prev => ({
                    ...prev,
                    [key]: {
                        columns: seedStructure(viewingTable.tableName),
                        data: seedData(viewingTable.tableName)
                    }
                }));
            }
            setSelectedIds([]); // Reset selection on table change
        }
    }, [viewingTable]);

    // --- CRUD ACTIONS ---

    const handleSave = () => {
        if (!viewingTable) return;
        const key = `${viewingTable.dbName}_${viewingTable.tableName}`;
        const currentStore = dbStore[key] || getTableContext(viewingTable.dbName, viewingTable.tableName);
        
        if (viewingTable.mode === 'BROWSE') {
            // Saving Row Data
            let newRows = [...currentStore.data];
            if (editTargetIndex !== null) {
                // Update
                newRows[editTargetIndex] = { ...newRows[editTargetIndex], ...formData };
            } else {
                // Create (Auto ID logic usually backend, simple max+1 here)
                const maxId = newRows.reduce((max, r) => (r.id > max ? r.id : max), 0);
                newRows.push({ ...formData, id: maxId + 1 });
            }
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, data: newRows } }));

        } else {
            // Saving Column Structure
            let newCols = [...currentStore.columns];
            if (editTargetIndex !== null) {
                // Update Column
                newCols[editTargetIndex] = formData as ColumnDef;
            } else {
                // Add Column
                newCols.push(formData as ColumnDef);
            }
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, columns: newCols } }));
        }

        setIsEditingItem(false);
        setEditTargetIndex(null);
        setFormData({});
    };

    const handleDeleteItem = (index: number) => {
        if (!viewingTable) return;
        if (!confirm("Are you sure you want to delete this item?")) return;

        const key = `${viewingTable.dbName}_${viewingTable.tableName}`;
        const currentStore = dbStore[key];

        if (viewingTable.mode === 'BROWSE') {
            const newRows = currentStore.data.filter((_, i) => i !== index);
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, data: newRows } }));
        } else {
            const newCols = currentStore.columns.filter((_, i) => i !== index);
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, columns: newCols } }));
        }
    };

    const handleBulkDelete = () => {
        if (!viewingTable || selectedIds.length === 0) return;
        if (!confirm(`Delete ${selectedIds.length} items?`)) return;

        const key = `${viewingTable.dbName}_${viewingTable.tableName}`;
        const currentStore = dbStore[key];

        if (viewingTable.mode === 'BROWSE') {
            // selectedIds are indices for now to keep it simple, or IDs
            // Let's assume selectedIds holds the actual indices for simplicity in this mock
            const newRows = currentStore.data.filter((_, i) => !selectedIds.includes(i));
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, data: newRows } }));
        } else {
             // For structure, selectedIds holds column indices
            const newCols = currentStore.columns.filter((_, i) => !selectedIds.includes(i));
            setDbStore(prev => ({ ...prev, [key]: { ...currentStore, columns: newCols } }));
        }
        setSelectedIds([]);
    };

    const toggleSelection = (id: number | string) => {
        setSelectedIds(prev => 
            prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]
        );
    };

    const toggleSelectAll = (allIds: (number | string)[]) => {
        if (selectedIds.length === allIds.length) {
            setSelectedIds([]);
        } else {
            setSelectedIds(allIds);
        }
    };

    const openEditor = (item: any = null, index: number | null = null) => {
        if (item) {
            setFormData({ ...item });
            setEditTargetIndex(index);
        } else {
            setFormData({});
            setEditTargetIndex(null);
        }
        setIsEditingItem(true);
    };

    // --- RENDER HELPERS ---

    const toggleDbExpansion = (id: string) => {
        setExpandedDb(prev => prev === id ? null : id);
    };

    const handleCreateDatabase = async () => {
        if (!targetSiteId) return;
        setIsSubmitting(true);
        try {
            await api.sites.update(targetSiteId, { hasDatabase: true });
            onRefresh();
            setIsCreating(false);
            setTargetSiteId('');
        } catch (e) {
            alert("Failed to create database");
        } finally {
            setIsSubmitting(false);
        }
    };

    const confirmDropDatabase = async () => {
        if (!dbToDelete) return;
        setIsDeleting(true);
        try {
            if (dbToDelete.status === SiteStatus.DB_ONLY) {
                await api.sites.delete(dbToDelete.id, true);
            } else {
                await api.sites.update(dbToDelete.id, { hasDatabase: false });
            }
            onRefresh();
            setDbToDelete(null);
        } catch (e) {
            alert("Failed to drop database");
        } finally {
            setIsDeleting(false);
        }
    };

    const openTableModal = (dbName: string, tableName: string, mode: 'BROWSE' | 'STRUCTURE') => {
        setViewingTable({ dbName, tableName, mode });
        setIsEditingItem(false);
        setSelectedIds([]);
    };

    // Get current context safely
    const currentTableData = viewingTable ? (dbStore[`${viewingTable.dbName}_${viewingTable.tableName}`] || getTableContext(viewingTable.dbName, viewingTable.tableName)) : { columns: [], data: [] };

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 relative">
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

            {/* 2. MASTER CREDENTIALS CARD */}
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
                     <div className="absolute right-0 bottom-0 opacity-5 pointer-events-none">
                        <Terminal className="w-64 h-64 text-white" />
                     </div>

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
                 <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2 px-1">
                        <Database className="w-5 h-5 text-indigo-600" />
                        Your Databases
                        <span className="text-xs font-normal text-slate-500 bg-slate-100 px-2 py-1 rounded-full">{dbSites.length} Active</span>
                    </h3>
                    
                    <button 
                        onClick={() => setIsCreating(true)}
                        className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center justify-center gap-2"
                    >
                        <Plus className="w-4 h-4" /> New Database
                    </button>
                 </div>

                 {dbSites.length > 0 ? (
                    <div className="flex flex-col gap-4">
                    {dbSites.map((site, idx) => {
                        const dbName = `db_${site.subdomain.replace(/[^a-z0-9]/g, '')}`;
                        const isExpanded = expandedDb === site.id;
                        const tables = getMockTables(site);
                        const totalRows = tables.reduce((acc, t) => acc + t.rows, 0);
                        const isOrphan = site.status === SiteStatus.DB_ONLY;

                        return (
                            <div key={site.id} className={`bg-white rounded-xl shadow-sm border transition-all duration-300 overflow-hidden ${isExpanded ? 'border-indigo-200 ring-2 ring-indigo-50 shadow-md' : 'border-slate-200 hover:border-indigo-300'} ${isOrphan ? 'bg-slate-50/50' : ''}`}>
                                {/* Header / Summary Row */}
                                <div 
                                    className="p-5 flex flex-col md:flex-row justify-between items-center gap-4 cursor-pointer bg-slate-50/30 hover:bg-slate-50 transition-colors"
                                    onClick={() => toggleDbExpansion(site.id)}
                                >
                                    <div className="flex items-center gap-4 w-full md:w-auto">
                                        <div className={`w-12 h-12 rounded-lg flex items-center justify-center border shadow-sm transition-colors ${isExpanded ? 'bg-indigo-600 text-white border-indigo-600' : isOrphan ? 'bg-slate-100 text-slate-400 border-slate-200' : 'bg-white text-indigo-600 border-slate-200'}`}>
                                            <Database className="w-6 h-6" />
                                        </div>
                                        <div>
                                            <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Database Name</div>
                                            <h4 className="font-bold text-slate-900 font-mono text-lg flex items-center gap-2">
                                                {dbName}
                                                {isOrphan && <span className="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full border border-amber-200 font-sans">Detached</span>}
                                            </h4>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                                        <div className="hidden md:block text-right">
                                            <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Linked Project</div>
                                            <div className="font-medium text-slate-700 flex items-center gap-1 justify-end">
                                                {isOrphan ? (
                                                    <span className="text-slate-400 flex items-center gap-1 italic"><Unlink className="w-3 h-3" /> None (Deleted)</span>
                                                ) : (
                                                    <span className="flex items-center gap-1"><Link className="w-3 h-3" /> {site.name}</span>
                                                )}
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
                                                                            <button 
                                                                                onClick={() => openTableModal(dbName, t.name, 'BROWSE')}
                                                                                className="text-xs text-indigo-600 hover:underline hover:text-indigo-800 font-medium"
                                                                            >
                                                                                Browse
                                                                            </button>
                                                                            <span className="text-slate-300">|</span>
                                                                            <button 
                                                                                onClick={() => openTableModal(dbName, t.name, 'STRUCTURE')}
                                                                                className="text-xs text-indigo-600 hover:underline hover:text-indigo-800 font-medium"
                                                                            >
                                                                                Structure
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                            <div className="mt-4 flex justify-end">
                                                <button 
                                                    onClick={() => setDbToDelete(site)}
                                                    className="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded transition-colors flex items-center gap-1"
                                                >
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
                            You haven't created any databases yet. To create one, use the "New Database" button above.
                        </p>
                    </div>
                 )}
            </div>

            {/* Create Database Modal */}
            {isCreating && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setIsCreating(false)} />
                    <div className="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                        <div className="flex justify-between items-start mb-6">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-indigo-50 rounded-lg">
                                    <Database className="w-6 h-6 text-indigo-600" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-bold text-slate-900">Create New Database</h3>
                                    <p className="text-xs text-slate-500">Attach a database to an existing project.</p>
                                </div>
                            </div>
                            <button onClick={() => setIsCreating(false)} className="text-slate-400 hover:text-slate-600">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Select Project</label>
                                {sitesWithoutDb.length > 0 ? (
                                    <select 
                                        value={targetSiteId}
                                        onChange={(e) => setTargetSiteId(e.target.value)}
                                        className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white text-sm"
                                    >
                                        <option value="" disabled>-- Choose a project --</option>
                                        {sitesWithoutDb.map(site => (
                                            <option key={site.id} value={site.id}>{site.name} ({site.subdomain})</option>
                                        ))}
                                    </select>
                                ) : (
                                    <div className="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                                        All your active projects already have a database.
                                    </div>
                                )}
                            </div>
                            
                            {targetSiteId && (
                                <div className="p-3 bg-slate-50 rounded-lg border border-slate-200 text-sm">
                                    <p className="text-slate-600 mb-1">Database Name will be:</p>
                                    <code className="font-mono font-bold text-indigo-600">
                                        db_{sitesWithoutDb.find(s => s.id === targetSiteId)?.subdomain.replace(/[^a-z0-9]/g, '')}
                                    </code>
                                </div>
                            )}

                            <div className="pt-4 flex justify-end gap-3">
                                <button 
                                    onClick={() => setIsCreating(false)}
                                    className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors"
                                >
                                    Cancel
                                </button>
                                <button 
                                    onClick={handleCreateDatabase}
                                    disabled={!targetSiteId || isSubmitting}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    {isSubmitting && <Loader2 className="w-4 h-4 animate-spin" />}
                                    Create Database
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
            
            {/* Delete/Drop Database Confirmation Modal */}
            {dbToDelete && (
                <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setDbToDelete(null)} />
                    <div className="relative w-full max-w-sm bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                        <div className="flex items-start gap-4">
                            <div className="p-3 bg-red-100 rounded-full shrink-0"><AlertTriangle className="w-6 h-6 text-red-600" /></div>
                            <div>
                                <h3 className="text-lg font-bold text-slate-900">Drop Database?</h3>
                                <p className="text-sm text-slate-500 mt-1">
                                    Are you sure you want to drop <span className="font-bold text-slate-800">db_{dbToDelete.subdomain.replace(/[^a-z0-9]/g, '')}</span>?
                                </p>
                                <div className="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg text-xs text-red-700">
                                    <p className="font-bold mb-1">Warning: Irreversible Action</p>
                                    <ul className="list-disc pl-4 space-y-1">
                                        <li>All tables and data will be permanently deleted.</li>
                                        {dbToDelete.status === SiteStatus.DB_ONLY ? (
                                            <li>This database is detached, so the item will be removed from your list entirely.</li>
                                        ) : (
                                            <li>The linked site <b>{dbToDelete.name}</b> will lose database connectivity.</li>
                                        )}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="mt-6 flex justify-end gap-3">
                            <button 
                                onClick={() => setDbToDelete(null)} 
                                disabled={isDeleting}
                                className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors"
                            >
                                Cancel
                            </button>
                            <button 
                                onClick={confirmDropDatabase} 
                                disabled={isDeleting}
                                className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2"
                            >
                                {isDeleting && <Loader2 className="w-4 h-4 animate-spin" />}
                                Confirm Drop
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Table Viewer Modal (Browse / Structure) */}
            {viewingTable && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" onClick={() => setViewingTable(null)} />
                    <div className="relative w-full max-w-5xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200 overflow-hidden">
                         {/* Header */}
                         <div className="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50 rounded-t-xl shrink-0">
                            <div className="flex items-center gap-4">
                                <div className="p-2 bg-indigo-100 rounded-lg text-indigo-700">
                                    <Table className="w-5 h-5" />
                                </div>
                                <div>
                                    <div className="flex items-center gap-2 text-sm text-slate-500 mb-0.5">
                                        <Database className="w-3 h-3" /> {viewingTable.dbName}
                                    </div>
                                    <h3 className="text-lg font-bold text-slate-900 font-mono">
                                        {viewingTable.tableName}
                                    </h3>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                                    <button 
                                        onClick={() => openTableModal(viewingTable.dbName, viewingTable.tableName, 'BROWSE')}
                                        className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${viewingTable.mode === 'BROWSE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}
                                    >
                                        <List className="w-3.5 h-3.5" /> Browse
                                    </button>
                                    <button 
                                        onClick={() => openTableModal(viewingTable.dbName, viewingTable.tableName, 'STRUCTURE')}
                                        className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${viewingTable.mode === 'STRUCTURE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}
                                    >
                                        <Settings className="w-3.5 h-3.5" /> Structure
                                    </button>
                                </div>
                                <button onClick={() => setViewingTable(null)} className="p-2 hover:bg-slate-200 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                                    <X className="w-5 h-5" />
                                </button>
                            </div>
                         </div>
                         
                         {/* Toolbar */}
                         <div className="px-6 py-2 border-b border-slate-100 flex items-center justify-between bg-white text-xs shrink-0">
                             <div className="flex items-center gap-3">
                                 {viewingTable.mode === 'BROWSE' ? (
                                    <button onClick={() => openEditor()} className="px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded hover:bg-emerald-100 flex items-center gap-1 font-medium transition-colors">
                                        <Plus className="w-3 h-3" /> Insert Row
                                    </button>
                                 ) : (
                                    <button onClick={() => openEditor()} className="px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded hover:bg-emerald-100 flex items-center gap-1 font-medium transition-colors">
                                        <Plus className="w-3 h-3" /> Add Column
                                    </button>
                                 )}
                                 
                                 {selectedIds.length > 0 && (
                                     <button onClick={handleBulkDelete} className="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded hover:bg-red-100 flex items-center gap-1 font-medium transition-colors animate-in fade-in">
                                         <Trash2 className="w-3 h-3" /> Delete Selected ({selectedIds.length})
                                     </button>
                                 )}
                             </div>

                             <div className="flex items-center gap-2 text-slate-500">
                                <div className="relative">
                                    <Search className="w-3 h-3 absolute left-2 top-1.5 text-slate-400" />
                                    <input type="text" placeholder="Search..." className="pl-7 pr-3 py-1 border border-slate-200 rounded text-xs focus:outline-none focus:border-indigo-400 w-32" />
                                </div>
                                <button className="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-indigo-600"><RefreshCw className="w-3.5 h-3.5" /></button>
                             </div>
                         </div>

                         {/* Content */}
                         <div className="flex-1 overflow-auto bg-slate-50 p-0 relative">
                            {viewingTable.mode === 'BROWSE' ? (
                                <div className="inline-block min-w-full align-middle">
                                    <table className="min-w-full divide-y divide-slate-200 border-b border-slate-200">
                                        <thead className="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                            <tr>
                                                <th scope="col" className="w-12 px-4 py-3 text-center">
                                                    <input 
                                                        type="checkbox" 
                                                        className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" 
                                                        checked={currentTableData.data.length > 0 && selectedIds.length === currentTableData.data.length}
                                                        onChange={() => toggleSelectAll(currentTableData.data.map((_, i) => i))}
                                                    />
                                                </th>
                                                {currentTableData.columns.map((col) => (
                                                    <th key={col.name} scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider font-mono border-l border-slate-200 whitespace-nowrap">
                                                        {col.name}
                                                    </th>
                                                ))}
                                                <th scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider border-l border-slate-200 bg-slate-100 sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-slate-100">
                                            {currentTableData.data.length > 0 ? (
                                                currentTableData.data.map((row: any, idx) => (
                                                    <tr key={idx} className={`hover:bg-indigo-50/20 ${selectedIds.includes(idx) ? 'bg-indigo-50/30' : ''}`}>
                                                        <td className="px-4 py-3 text-center">
                                                            <input 
                                                                type="checkbox" 
                                                                className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                                checked={selectedIds.includes(idx)}
                                                                onChange={() => toggleSelection(idx)}
                                                            />
                                                        </td>
                                                        {currentTableData.columns.map((col, vIdx) => (
                                                            <td key={vIdx} className="px-4 py-3 whitespace-nowrap text-sm text-slate-600 font-mono border-l border-slate-100 max-w-[200px] truncate" title={String(row[col.name])}>
                                                                {row[col.name] === null ? <span className="text-slate-300 italic">NULL</span> : String(row[col.name])}
                                                            </td>
                                                        ))}
                                                        <td className="px-4 py-3 whitespace-nowrap text-sm text-right border-l border-slate-100 bg-white sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">
                                                            <div className="flex gap-2 justify-end">
                                                                <button onClick={() => openEditor(row, idx)} className="text-indigo-600 hover:text-indigo-800 text-xs flex items-center gap-1"><Edit2 className="w-3 h-3" /> Edit</button>
                                                                <button onClick={() => handleDeleteItem(idx)} className="text-red-600 hover:text-red-800 text-xs flex items-center gap-1"><Trash2 className="w-3 h-3" /> Del</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ))
                                            ) : (
                                                <tr>
                                                    <td colSpan={currentTableData.columns.length + 2} className="px-6 py-12 text-center text-slate-500 italic bg-white">
                                                        This table is empty.
                                                    </td>
                                                </tr>
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                // STRUCTURE VIEW
                                <div className="inline-block min-w-full align-middle">
                                    <table className="min-w-full divide-y divide-slate-200 border-b border-slate-200">
                                        <thead className="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                            <tr>
                                                <th className="w-12 px-4 py-3 text-center">
                                                     <input 
                                                        type="checkbox" 
                                                        className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" 
                                                        checked={currentTableData.columns.length > 0 && selectedIds.length === currentTableData.columns.length}
                                                        onChange={() => toggleSelectAll(currentTableData.columns.map((_, i) => i))}
                                                    />
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">#</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Name</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Type</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Collation</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Null</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Default</th>
                                                <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Extra</th>
                                                <th className="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase tracking-wider sticky right-0 bg-slate-100">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-slate-100">
                                            {currentTableData.columns.map((col, idx) => (
                                                <tr key={idx} className={`hover:bg-slate-50 ${selectedIds.includes(idx) ? 'bg-indigo-50/30' : ''}`}>
                                                     <td className="px-4 py-3 text-center">
                                                            <input 
                                                                type="checkbox" 
                                                                className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                                checked={selectedIds.includes(idx)}
                                                                onChange={() => toggleSelection(idx)}
                                                            />
                                                    </td>
                                                    <td className="px-4 py-3 text-xs text-slate-400 font-mono">{idx + 1}</td>
                                                    <td className="px-4 py-3 text-sm font-bold text-slate-700 font-mono flex items-center gap-2">
                                                        {col.key === 'PRI' && <Key className="w-3 h-3 text-amber-500 fill-amber-100" />}
                                                        {col.key === 'UNI' && <Key className="w-3 h-3 text-slate-400" />}
                                                        {col.name}
                                                    </td>
                                                    <td className="px-4 py-3 text-sm text-indigo-600 font-mono uppercase">{col.type}</td>
                                                    <td className="px-4 py-3 text-xs text-slate-500 font-mono">{col.collation || '-'}</td>
                                                    <td className="px-4 py-3 text-xs text-slate-600 font-bold">{col.null}</td>
                                                    <td className="px-4 py-3 text-xs text-slate-500 font-mono">{col.default || <span className="italic text-slate-300">None</span>}</td>
                                                    <td className="px-4 py-3 text-xs text-slate-500 font-mono uppercase">{col.extra || '-'}</td>
                                                    <td className="px-4 py-3 text-right sticky right-0 bg-white">
                                                        <div className="flex justify-end gap-3 text-slate-400">
                                                            <button onClick={() => openEditor(col, idx)} className="hover:text-indigo-600"><Edit2 className="w-3.5 h-3.5" /></button>
                                                            <button onClick={() => handleDeleteItem(idx)} className="hover:text-red-600"><Trash2 className="w-3.5 h-3.5" /></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                         </div>

                         {/* Footer */}
                         <div className="px-6 py-3 border-t border-slate-200 bg-slate-50 rounded-b-xl flex justify-between items-center text-xs text-slate-500 shrink-0">
                            <div>
                                {viewingTable.mode === 'BROWSE' 
                                    ? `Total: ${currentTableData.data.length} row(s)` 
                                    : `Total: ${currentTableData.columns.length} column(s)`
                                }
                            </div>
                            <div className="font-mono text-[10px] text-slate-400">
                                Query took 0.0001 sec (Memory)
                            </div>
                         </div>
                    </div>

                    {/* Editor Modal (Sub-Modal) */}
                    {isEditingItem && (
                        <div className="absolute inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                             <div className="bg-white rounded-lg shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 fade-in duration-200">
                                <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                    <h3 className="font-bold text-slate-800">
                                        {editTargetIndex !== null ? 'Edit' : 'Insert'} {viewingTable.mode === 'BROWSE' ? 'Row' : 'Column'}
                                    </h3>
                                    <button onClick={() => setIsEditingItem(false)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                                </div>
                                <div className="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                                    {viewingTable.mode === 'BROWSE' ? (
                                        // ROW EDITOR
                                        currentTableData.columns.map(col => (
                                            <div key={col.name} className="space-y-1">
                                                <label className="text-xs font-bold text-slate-500 uppercase">{col.name} <span className="text-indigo-400 font-normal normal-case">({col.type})</span></label>
                                                {col.extra === 'auto_increment' && editTargetIndex === null ? (
                                                     <input type="text" disabled value="(Auto Increment)" className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-slate-500 italic text-sm" />
                                                ) : (
                                                    <input 
                                                        type="text" 
                                                        value={formData[col.name] || ''} 
                                                        onChange={e => setFormData({...formData, [col.name]: e.target.value})}
                                                        className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-mono text-slate-700"
                                                        placeholder={col.default === 'NULL' ? 'NULL' : ''}
                                                    />
                                                )}
                                            </div>
                                        ))
                                    ) : (
                                        // COLUMN EDITOR
                                        <>
                                            <div className="space-y-1">
                                                <label className="text-xs font-bold text-slate-500 uppercase">Column Name</label>
                                                <input type="text" value={formData.name || ''} onChange={e => setFormData({...formData, name: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm" />
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="space-y-1">
                                                    <label className="text-xs font-bold text-slate-500 uppercase">Type</label>
                                                    <select value={formData.type || 'varchar(255)'} onChange={e => setFormData({...formData, type: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm bg-white">
                                                        <option value="int(11)">INT</option>
                                                        <option value="bigint(20)">BIGINT</option>
                                                        <option value="varchar(255)">VARCHAR</option>
                                                        <option value="text">TEXT</option>
                                                        <option value="date">DATE</option>
                                                        <option value="timestamp">TIMESTAMP</option>
                                                        <option value="boolean">BOOLEAN</option>
                                                    </select>
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-xs font-bold text-slate-500 uppercase">Default</label>
                                                    <input type="text" value={formData.default || ''} onChange={e => setFormData({...formData, default: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="NULL" />
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="space-y-1">
                                                     <label className="text-xs font-bold text-slate-500 uppercase">Attributes</label>
                                                     <div className="flex flex-col gap-2 p-2 border border-slate-200 rounded">
                                                         <label className="flex items-center gap-2 text-sm text-slate-600">
                                                             <input type="checkbox" checked={formData.null === 'YES'} onChange={e => setFormData({...formData, null: e.target.checked ? 'YES' : 'NO'})} /> Nullable
                                                         </label>
                                                         <label className="flex items-center gap-2 text-sm text-slate-600">
                                                             <input type="checkbox" checked={formData.key === 'PRI'} onChange={e => setFormData({...formData, key: e.target.checked ? 'PRI' : ''})} /> Primary Key
                                                         </label>
                                                          <label className="flex items-center gap-2 text-sm text-slate-600">
                                                             <input type="checkbox" checked={formData.extra === 'auto_increment'} onChange={e => setFormData({...formData, extra: e.target.checked ? 'auto_increment' : ''})} /> Auto Increment
                                                         </label>
                                                     </div>
                                                </div>
                                            </div>
                                        </>
                                    )}
                                </div>
                                <div className="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                                    <button onClick={() => setIsEditingItem(false)} className="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded font-medium text-sm">Cancel</button>
                                    <button onClick={handleSave} className="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded font-medium text-sm shadow-sm flex items-center gap-2">
                                        <Save className="w-4 h-4" /> Save
                                    </button>
                                </div>
                             </div>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};