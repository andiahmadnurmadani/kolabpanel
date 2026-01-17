import React, { useState, useEffect } from 'react';
import { User, Site, SiteStatus } from '../../types';
import { api } from '../../services/api';
import { Database, Server, ExternalLink, Trash2, Link, Table, ChevronDown, ChevronUp, FileSpreadsheet, Plus, X, Loader2, Unlink, AlertTriangle, Download, Upload } from 'lucide-react';
import { MasterCredentials } from '../../components/database/MasterCredentials';
import { TableViewer } from '../../components/database/TableViewer';
import { CreateTableModal } from '../../components/database/CreateTableModal';

const API_Base = typeof window !== 'undefined' ? window.location.origin + '/api' : 'http://localhost:5000/api';

interface DatabaseManagerProps {
    sites: Site[];
    user: User;
    onRefresh: () => void;
}

interface DBTable {
    name: string;
    rows: number;
    size: string;
    engine: string;
    collation: string;
}

interface TableViewState {
    dbName: string;
    tableName: string;
    mode: 'BROWSE' | 'STRUCTURE';
    siteId: string;
}

export const DatabaseManager: React.FC<DatabaseManagerProps> = ({ sites, user, onRefresh }) => {
    if (!user || !user.username) {
        return <div className="p-10 text-center text-slate-500">Loading profile data...</div>;
    }

    const safeSites = Array.isArray(sites) ? sites : [];
    const dbSites = safeSites.filter(site => site.hasDatabase);
    const sitesWithoutDb = safeSites.filter(site => !site.hasDatabase && site.status !== 'FAILED' && site.status !== SiteStatus.DB_ONLY);

    const [expandedDb, setExpandedDb] = useState<string | null>(null);
    const [isCreating, setIsCreating] = useState(false);
    const [targetSiteId, setTargetSiteId] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [dbToDelete, setDbToDelete] = useState<Site | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Real Data State
    const [tablesMap, setTablesMap] = useState<Record<string, DBTable[]>>({});
    const [loadingTables, setLoadingTables] = useState<string | null>(null);

    // Table Viewer State
    const [viewingTable, setViewingTable] = useState<TableViewState | null>(null);
    // Table Creation State
    const [creatingTableInDb, setCreatingTableInDb] = useState<{ siteId: string, dbName: string } | null>(null);

    const copyToClipboard = (text: string) => {
        navigator.clipboard.writeText(text);
    };

    const refreshTables = (siteId: string) => {
        setLoadingTables(siteId);
        api.database.getTables(siteId)
            .then((tables: any) => {
                setTablesMap(prev => ({ ...prev, [siteId]: tables }));
            })
            .catch(err => console.error("Failed to fetch tables", err))
            .finally(() => setLoadingTables(null));
    };

    // Fetch tables when expanded
    useEffect(() => {
        if (expandedDb) {
            refreshTables(expandedDb);
        }
    }, [expandedDb]);

    // --- ACTIONS ---
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

    const handleExport = (siteId: string) => {
        window.open(`${API_Base}/database/${siteId}/export`, '_blank');
    };

    const handleImport = async (siteId: string, file: File) => {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = async (e) => {
            const sql = e.target?.result as string;
            if (!sql) return;

            if (!confirm(`WARNING: Importing SQL will execute commands directly against your database 'db_...'. existing tables might be dropped if the SQL contains DROP TABLE commands.\n\nAre you sure you want to proceed?`)) return;

            try {
                // We need to send this to the import endpoint
                const res = await fetch(`${API_Base}/database/${siteId}/import`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}` // simple assumption using saved token
                    },
                    body: JSON.stringify({ sql })
                });

                if (!res.ok) {
                    const err = await res.json();
                    throw new Error(err.message || 'Import failed');
                }

                alert("Import successful! Tables refreshed.");
                refreshTables(siteId);
            } catch (err: any) {
                alert("Import error: " + err.message);
            }
        };
        reader.readAsText(file);
    };

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 relative">
            {/* Header & Status */}
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
                            <span className="text-indigo-600 font-medium">sql_{user.username.replace(/[^a-z0-9]/g, '')}</span>
                        </div>
                    </div>
                </div>
                {/* phpMyAdmin button removed */}
            </div>

            <MasterCredentials user={user} copyToClipboard={copyToClipboard} />

            <div className="space-y-4">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 className="text-lg font-bold text-slate-800 flex items-center gap-2 px-1">
                        <Database className="w-5 h-5 text-indigo-600" />
                        Your Databases
                        <span className="text-xs font-normal text-slate-500 bg-slate-100 px-2 py-1 rounded-full">{dbSites.length} Active</span>
                    </h3>
                    <button onClick={() => setIsCreating(true)} className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center justify-center gap-2">
                        <Plus className="w-4 h-4" /> New Database
                    </button>
                </div>

                {dbSites.length > 0 ? (
                    <div className="flex flex-col gap-4">
                        {dbSites.map((site) => {
                            const dbName = `db_${site.subdomain.replace(/[^a-z0-9]/g, '')}`;
                            const isExpanded = expandedDb === site.id;
                            const tables = tablesMap[site.id] || [];
                            const totalRows = tables.reduce((acc, t) => acc + t.rows, 0);
                            const isOrphan = site.status === SiteStatus.DB_ONLY;
                            const isLoading = loadingTables === site.id;

                            return (
                                <div key={site.id} className={`bg-white rounded-xl shadow-sm border transition-all duration-300 overflow-hidden ${isExpanded ? 'border-indigo-200 ring-2 ring-indigo-50 shadow-md' : 'border-slate-200 hover:border-indigo-300'} ${isOrphan ? 'bg-slate-50/50' : ''}`}>
                                    <div className="p-5 flex flex-col md:flex-row justify-between items-center gap-4 cursor-pointer bg-slate-50/30 hover:bg-slate-50 transition-colors" onClick={() => setExpandedDb(isExpanded ? null : site.id)}>
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
                                                    {isOrphan ? (<span className="text-slate-400 flex items-center gap-1 italic"><Unlink className="w-3 h-3" /> None (Deleted)</span>) : (<span className="flex items-center gap-1"><Link className="w-3 h-3" /> {site.name}</span>)}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <div className="hidden sm:flex flex-col items-end mr-2">
                                                    <span className="text-xs font-bold text-slate-700">{isLoading ? 'Loading...' : `${tables.length} Tables`}</span>
                                                    {!isLoading && <span className="text-[10px] text-slate-500">~{totalRows} Rows</span>}
                                                </div>
                                                <button className={`p-2 rounded-lg transition-colors border ${isExpanded ? 'bg-indigo-50 text-indigo-600 border-indigo-100' : 'bg-white text-slate-400 border-slate-200'}`}>
                                                    {isExpanded ? <ChevronUp className="w-5 h-5" /> : <ChevronDown className="w-5 h-5" />}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {isExpanded && (
                                        <div className="border-t border-slate-200 animate-in slide-in-from-top-2 duration-200">
                                            <div className="p-6 bg-slate-50/50">
                                                <div className="flex items-center justify-between mb-4">
                                                    <h5 className="font-bold text-slate-800 flex items-center gap-2"><Table className="w-4 h-4 text-slate-500" /> Database Tables</h5>
                                                    <div className="flex items-center gap-3">
                                                        <div className="text-xs text-slate-500 bg-white px-2 py-1 rounded border border-slate-200">Showing {tables.length} tables</div>
                                                        <div className="flex gap-2">
                                                            <button
                                                                onClick={() => handleExport(site.id)}
                                                                className="text-xs flex items-center gap-1 bg-white hover:bg-indigo-50 text-indigo-600 border border-slate-200 hover:border-indigo-200 px-3 py-1.5 rounded-lg transition-colors font-medium shadow-sm"
                                                                title="Export Database to SQL"
                                                            >
                                                                <Download className="w-3 h-3" /> Export SQL
                                                            </button>
                                                            <div className="relative">
                                                                <input
                                                                    type="file"
                                                                    accept=".sql"
                                                                    onChange={(e) => {
                                                                        if (e.target.files?.[0]) handleImport(site.id, e.target.files[0]);
                                                                        e.target.value = ''; // reset
                                                                    }}
                                                                    className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                                />
                                                                <button
                                                                    className="text-xs flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white border border-transparent px-3 py-1.5 rounded-lg transition-colors font-medium shadow-sm"
                                                                    title="Import SQL File"
                                                                >
                                                                    <Upload className="w-3 h-3" /> Import SQL
                                                                </button>
                                                            </div>
                                                            <button
                                                                onClick={() => setCreatingTableInDb({ siteId: site.id, dbName })}
                                                                className="text-xs flex items-center gap-1 bg-white hover:bg-indigo-50 text-indigo-600 border border-slate-200 hover:border-indigo-200 px-3 py-1.5 rounded-lg transition-colors font-medium shadow-sm"
                                                            >
                                                                <Plus className="w-3 h-3" /> Create Table
                                                            </button>
                                                        </div>
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
                                                                {isLoading ? (
                                                                    <tr>
                                                                        <td colSpan={6} className="px-6 py-10 text-center text-slate-500">
                                                                            <div className="flex justify-center mb-2"><Loader2 className="w-6 h-6 animate-spin text-indigo-500" /></div>
                                                                            Fetching tables...
                                                                        </td>
                                                                    </tr>
                                                                ) : tables.length > 0 ? (
                                                                    tables.map((t, i) => (
                                                                        <tr key={i} className="hover:bg-indigo-50/30 transition-colors group">
                                                                            <td className="px-4 py-3 font-medium text-slate-700 flex items-center gap-2"><FileSpreadsheet className="w-4 h-4 text-slate-400 group-hover:text-indigo-500" /> {t.name}</td>
                                                                            <td className="px-4 py-3 text-slate-600">{t.rows.toLocaleString()}</td>
                                                                            <td className="px-4 py-3 text-slate-600 font-mono text-xs">{t.size}</td>
                                                                            <td className="px-4 py-3 text-slate-500 text-xs">{t.engine}</td>
                                                                            <td className="px-4 py-3 text-slate-500 text-xs">{t.collation}</td>
                                                                            <td className="px-4 py-3 text-right">
                                                                                <div className="flex justify-end gap-2 opacity-50 group-hover:opacity-100 transition-opacity">
                                                                                    <button onClick={() => setViewingTable({ dbName, tableName: t.name, mode: 'BROWSE', siteId: site.id })} className="text-xs text-indigo-600 hover:underline hover:text-indigo-800 font-medium">Browse</button>
                                                                                    <span className="text-slate-300">|</span>
                                                                                    <button onClick={() => setViewingTable({ dbName, tableName: t.name, mode: 'STRUCTURE', siteId: site.id })} className="text-xs text-indigo-600 hover:underline hover:text-indigo-800 font-medium">Structure</button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    ))
                                                                ) : (
                                                                    <tr><td colSpan={6} className="px-6 py-8 text-center text-slate-500 italic">No tables found. Create one to get started!</td></tr>
                                                                )}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div className="mt-4 flex justify-end">
                                                    <button onClick={() => setDbToDelete(site)} className="text-xs text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded transition-colors flex items-center gap-1"><Trash2 className="w-3 h-3" /> Drop Database</button>
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
                        <div className="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4"><Database className="w-8 h-8" /></div>
                        <h3 className="text-lg font-bold text-slate-800">No Databases Found</h3>
                        <p className="text-slate-500 mt-2 max-w-sm mx-auto text-sm">You haven't created any databases yet.</p>
                    </div>
                )}
            </div>

            {/* Existing Modals ... */}
            {isCreating && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setIsCreating(false)} />
                    <div className="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                        <div className="flex justify-between items-start mb-6">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-indigo-50 rounded-lg"><Database className="w-6 h-6 text-indigo-600" /></div>
                                <div><h3 className="text-lg font-bold text-slate-900">Create New Database</h3><p className="text-xs text-slate-500">Attach a database to an existing project.</p></div>
                            </div>
                            <button onClick={() => setIsCreating(false)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-700 mb-2">Select Project</label>
                                {sitesWithoutDb.length > 0 ? (
                                    <select value={targetSiteId} onChange={(e) => setTargetSiteId(e.target.value)} className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white text-sm">
                                        <option value="" disabled>-- Choose a project --</option>
                                        {sitesWithoutDb.map(site => (
                                            <option key={site.id} value={site.id}>{site.name} ({site.subdomain})</option>
                                        ))}
                                    </select>
                                ) : (
                                    <div className="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">All your active projects already have a database.</div>
                                )}
                            </div>
                            {targetSiteId && (
                                <div className="p-3 bg-slate-50 rounded-lg border border-slate-200 text-sm">
                                    <p className="text-slate-600 mb-1">Database Name will be:</p>
                                    <code className="font-mono font-bold text-indigo-600">db_{sitesWithoutDb.find(s => s.id === targetSiteId)?.subdomain.replace(/[^a-z0-9]/g, '')}</code>
                                </div>
                            )}
                            <div className="pt-4 flex justify-end gap-3">
                                <button onClick={() => setIsCreating(false)} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                                <button onClick={handleCreateDatabase} disabled={!targetSiteId || isSubmitting} className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">{isSubmitting && <Loader2 className="w-4 h-4 animate-spin" />} Create Database</button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {dbToDelete && (
                <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={() => setDbToDelete(null)} />
                    <div className="relative w-full max-w-sm bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">
                        <div className="flex items-start gap-4">
                            <div className="p-3 bg-red-100 rounded-full shrink-0"><AlertTriangle className="w-6 h-6 text-red-600" /></div>
                            <div>
                                <h3 className="text-lg font-bold text-slate-900">Drop Database?</h3>
                                <p className="text-sm text-slate-500 mt-1">Are you sure you want to drop <span className="font-bold text-slate-800">db_{dbToDelete.subdomain.replace(/[^a-z0-9]/g, '')}</span>?</p>
                                <div className="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg text-xs text-red-700">
                                    <p className="font-bold mb-1">Warning: Irreversible Action</p>
                                    <ul className="list-disc pl-4 space-y-1">
                                        <li>All tables and data will be permanently deleted.</li>
                                        {dbToDelete.status === SiteStatus.DB_ONLY ? (<li>This database is detached, so the item will be removed from your list entirely.</li>) : (<li>The linked site <b>{dbToDelete.name}</b> will lose database connectivity.</li>)}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="mt-6 flex justify-end gap-3">
                            <button onClick={() => setDbToDelete(null)} disabled={isDeleting} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                            <button onClick={confirmDropDatabase} disabled={isDeleting} className="px-4 py-2 bg-red-600 text-white rounded-lg font-medium text-sm hover:bg-red-700 shadow-sm transition-colors flex items-center gap-2">{isDeleting && <Loader2 className="w-4 h-4 animate-spin" />} Confirm Drop</button>
                        </div>
                    </div>
                </div>
            )}

            {viewingTable && (
                <TableViewer
                    viewingTable={viewingTable}
                    onClose={() => setViewingTable(null)}
                />
            )}

            {creatingTableInDb && (
                <CreateTableModal
                    siteId={creatingTableInDb.siteId}
                    dbName={creatingTableInDb.dbName}
                    onClose={() => setCreatingTableInDb(null)}
                    onSuccess={() => {
                        refreshTables(creatingTableInDb.siteId);
                        setCreatingTableInDb(null);
                    }}
                />
            )}
        </div>
    );
};