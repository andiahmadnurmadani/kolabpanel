import React, { useState, useEffect } from 'react';
import { Table, List, Settings, Search, RefreshCw, Plus, Trash2, Edit2, Key, X, Save, Loader2, Database as DbIcon, AlertTriangle } from 'lucide-react';
import { api } from '../../services/api';
import { AddColumnModal } from './AddColumnModal';

interface ColumnDef {
    name: string;
    type: string;
    collation: string;
    null: 'YES' | 'NO';
    key: 'PRI' | 'UNI' | '';
    default: string | null;
    extra: string;
}

interface TableViewState {
    dbName: string;
    tableName: string;
    mode: 'BROWSE' | 'STRUCTURE';
    siteId: string;
}

interface TableViewerProps {
    viewingTable: TableViewState;
    onClose: () => void;
}

export const TableViewer: React.FC<TableViewerProps> = ({ viewingTable, onClose }) => {
    const [mode, setMode] = useState<'BROWSE' | 'STRUCTURE'>(viewingTable.mode);
    const [columns, setColumns] = useState<ColumnDef[]>([]);
    const [data, setData] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [pagination, setPagination] = useState({ page: 1, limit: 50, total: 0 });

    const [selectedIds, setSelectedIds] = useState<(number | string)[]>([]); // Use primary key values or indices
    const [isEditingItem, setIsEditingItem] = useState(false);
    const [editTargetRow, setEditTargetRow] = useState<any | null>(null); // For updates
    const [formData, setFormData] = useState<any>({});
    const [isSaving, setIsSaving] = useState(false);
    const [isAddingColumn, setIsAddingColumn] = useState(false);

    const primaryKey = columns.find(c => c.key === 'PRI')?.name || 'id'; // fallback to id

    useEffect(() => {
        setMode(viewingTable.mode);
    }, [viewingTable.mode]);

    useEffect(() => {
        fetchData();
    }, [viewingTable.tableName, viewingTable.siteId, mode, pagination.page]);

    const fetchData = async () => {
        setLoading(true);
        setError(null);
        try {
            // Always fetch structure first to know columns/primary keys
            const struct = await api.database.getTableStructure(viewingTable.siteId, viewingTable.tableName);
            // The API returns { name, type, ... } objects directly or in results? 
            // My route: res.json(results). DESCRIBE returns Field, Type, Null, Key, Default, Extra.
            // I need to map "Field" to "name".
            const formattedCols = (struct as any[]).map((c: any) => ({
                name: c.Field,
                type: c.Type,
                collation: c.Collation,
                null: c.Null,
                key: c.Key,
                default: c.Default,
                extra: c.Extra
            }));
            setColumns(formattedCols);

            if (mode === 'BROWSE') {
                const res = await api.database.getTableData(viewingTable.siteId, viewingTable.tableName, pagination.page, pagination.limit);
                setData(res.data);
                setPagination(prev => ({ ...prev, total: res.pagination.total }));
            } else {
                setData([]);
            }
        } catch (err: any) {
            setError(err.message || 'Failed to load table data');
        } finally {
            setLoading(false);
        }
    };

    const toggleSelection = (id: number | string) => {
        setSelectedIds(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
    };

    const toggleSelectAll = () => {
        if (selectedIds.length === data.length) {
            setSelectedIds([]);
        } else {
            // For selection, use the primary key value if possible, else index?
            // Deleting by index is risky if data changes. 
            // We really need a primary key for robust CRUD.
            // If no PK, allow selection by index but warn or disable delete?
            if (primaryKey) {
                setSelectedIds(data.map(row => row[primaryKey]));
            } else {
                // Fallback to strict object equality or something? 
                // For now, let's assume PK exists or use JSON stringify for identity (gross)
                // Just disable bulk actions without PK for safety?
                setSelectedIds([]);
                alert("Table has no Primary Key, bulk actions disabled.");
            }
        }
    };

    const openEditor = (row: any = null) => {
        if (row) {
            setFormData({ ...row });
            setEditTargetRow(row);
        } else {
            // Init empty form data with defaults
            const defaults: any = {};
            columns.forEach(c => {
                // don't set auto_increment fields
                if (c.extra !== 'auto_increment') {
                    defaults[c.name] = c.default === 'NULL' ? null : c.default || '';
                }
            });
            setFormData(defaults);
            setEditTargetRow(null);
        }
        setIsEditingItem(true);
    };

    const handleSave = async () => {
        setIsSaving(true);
        try {
            // Construct SQL
            // Very basic SQL generation. 
            // Note: In a real app we'd want parametrized queries via API.
            // Here we will manually escape strings (basic quote doubling).
            const escape = (val: any) => {
                if (val === null) return 'NULL';
                if (typeof val === 'number') return val;
                if (typeof val === 'boolean') return val ? 1 : 0;
                return `'${String(val).replace(/'/g, "''")}'`;
            };

            if (editTargetRow) {
                // UPDATE
                if (!primaryKey) throw new Error("Cannot update without Primary Key");

                const updates = Object.keys(formData)
                    .filter(k => formData[k] !== editTargetRow[k]) // Only changed fields
                    .map(k => `\`${k}\` = ${escape(formData[k])}`)
                    .join(', ');

                if (!updates) {
                    setIsEditingItem(false);
                    return; // No changes
                }

                const query = `UPDATE \`${viewingTable.tableName}\` SET ${updates} WHERE \`${primaryKey}\` = ${escape(editTargetRow[primaryKey])}`;
                await api.database.query(viewingTable.siteId, query);

            } else {
                // INSERT
                const keys = Object.keys(formData);
                const values = keys.map(k => escape(formData[k])).join(', ');
                const fields = keys.map(k => `\`${k}\``).join(', ');

                const query = `INSERT INTO \`${viewingTable.tableName}\` (${fields}) VALUES (${values})`;
                await api.database.query(viewingTable.siteId, query);
            }

            await fetchData();
            setIsEditingItem(false);
            setFormData({});
            setEditTargetRow(null);
        } catch (err: any) {
            alert("Failed to save: " + err.message);
        } finally {
            setIsSaving(false);
        }
    };

    const handleDelete = async (ids: (number | string)[]) => {
        if (!confirm(`Are you sure you want to delete ${ids.length} rows?`)) return;
        if (!primaryKey) {
            alert("Cannot delete without Primary Key");
            return;
        }

        const escape = (val: any) => {
            if (typeof val === 'number') return val;
            return `'${String(val).replace(/'/g, "''")}'`;
        };

        const idList = ids.map(id => escape(id)).join(',');
        const query = `DELETE FROM \`${viewingTable.tableName}\` WHERE \`${primaryKey}\` IN (${idList})`;

        try {
            await api.database.query(viewingTable.siteId, query);
            setSelectedIds([]);
            fetchData();
        } catch (err: any) {
            alert("Failed to delete: " + err.message);
        }
    };

    const handleDropColumn = async (colName: string) => {
        if (!confirm(`Are you sure you want to drop column '${colName}'? This implies DATA LOSS.`)) return;

        try {
            const query = `ALTER TABLE \`${viewingTable.tableName}\` DROP COLUMN \`${colName}\``;
            await api.database.query(viewingTable.siteId, query);
            fetchData();
        } catch (err: any) {
            alert("Failed to drop column: " + err.message);
        }
    };

    const handleDropTable = async () => {
        const confirmName = prompt(`To DELETE this table and ALL data, type '${viewingTable.tableName}':`);
        if (confirmName !== viewingTable.tableName) return;

        try {
            const query = `DROP TABLE \`${viewingTable.tableName}\``;
            await api.database.query(viewingTable.siteId, query);
            onClose(); // Close viewer as table is gone
        } catch (err: any) {
            alert("Failed to drop table: " + err.message);
        }
    };

    return (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" onClick={onClose} />
            <div className="relative w-full max-w-6xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200 overflow-hidden">
                {/* Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50 rounded-t-xl shrink-0">
                    <div className="flex items-center gap-4">
                        <div className="p-2 bg-indigo-100 rounded-lg text-indigo-700">
                            <Table className="w-5 h-5" />
                        </div>
                        <div>
                            <div className="flex items-center gap-2 text-sm text-slate-500 mb-0.5">
                                <DbIcon className="w-3 h-3" /> {viewingTable.dbName}
                            </div>
                            <h3 className="text-lg font-bold text-slate-900 font-mono">
                                {viewingTable.tableName}
                            </h3>
                        </div>
                    </div>
                    <div className="flex items-center gap-3">
                        <div className="flex bg-slate-100 p-1 rounded-lg border border-slate-200">
                            <button onClick={() => setMode('BROWSE')} className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${mode === 'BROWSE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}>
                                <List className="w-3.5 h-3.5" /> Browse
                            </button>
                            <button onClick={() => setMode('STRUCTURE')} className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${mode === 'STRUCTURE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}>
                                <Settings className="w-3.5 h-3.5" /> Structure
                            </button>
                        </div>
                        <button onClick={onClose} className="p-2 hover:bg-slate-200 rounded-full text-slate-400 hover:text-slate-600 transition-colors">
                            <X className="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {/* Toolbar */}
                <div className="px-6 py-2 border-b border-slate-100 flex items-center justify-between bg-white text-xs shrink-0">
                    <div className="flex items-center gap-3">
                        {mode === 'BROWSE' && (
                            <>
                                <button onClick={() => openEditor()} className="px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded hover:bg-emerald-100 flex items-center gap-1 font-medium transition-colors">
                                    <Plus className="w-3 h-3" /> Insert Row
                                </button>
                                {selectedIds.length > 0 && (
                                    <button onClick={() => handleDelete(selectedIds)} className="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded hover:bg-red-100 flex items-center gap-1 font-medium transition-colors animate-in fade-in">
                                        <Trash2 className="w-3 h-3" /> Delete Selected ({selectedIds.length})
                                    </button>
                                )}
                            </>
                        )}
                    </div>
                    {mode === 'STRUCTURE' && (
                        <div className="flex items-center gap-2">
                            <button onClick={() => setIsAddingColumn(true)} className="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-50 hover:border-indigo-200 transition-colors shadow-sm">
                                <Plus className="w-4 h-4" /> Add Column
                            </button>
                            <button onClick={handleDropTable} className="flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 hover:border-red-200 transition-colors shadow-sm ml-4">
                                <Trash2 className="w-4 h-4" /> Drop Table
                            </button>
                        </div>
                    )}
                    <div className="flex items-center gap-2 text-slate-500">
                        <button onClick={fetchData} disabled={loading} className="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-indigo-600 disabled:opacity-50">
                            <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} />
                        </button>
                    </div>
                </div>

                {/* Content */}
                <div className="flex-1 overflow-auto bg-slate-50 p-0 relative">
                    {loading && (
                        <div className="absolute inset-0 flex items-center justify-center bg-white/80 z-20">
                            <Loader2 className="w-8 h-8 animate-spin text-indigo-500" />
                        </div>
                    )}

                    {error && (
                        <div className="p-8 text-center text-red-500 flex flex-col items-center">
                            <AlertTriangle className="w-8 h-8 mb-2" />
                            {error}
                        </div>
                    )}

                    {!loading && !error && (
                        mode === 'BROWSE' ? (
                            <div className="inline-block min-w-full align-middle">
                                <table className="min-w-full divide-y divide-slate-200 border-b border-slate-200">
                                    <thead className="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th scope="col" className="w-12 px-4 py-3 text-center">
                                                <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={data.length > 0 && selectedIds.length === data.length} onChange={toggleSelectAll} />
                                            </th>
                                            {columns.map((col) => (
                                                <th key={col.name} scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider font-mono border-l border-slate-200 whitespace-nowrap">
                                                    {col.name}
                                                    {col.key === 'PRI' && <Key className="w-3 h-3 inline ml-1 text-amber-500" />}
                                                </th>
                                            ))}
                                            <th scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider border-l border-slate-200 bg-slate-100 sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-slate-100">
                                        {data.length > 0 ? (
                                            data.map((row: any, idx) => {
                                                const rowId = primaryKey ? row[primaryKey] : idx;
                                                return (
                                                    <tr key={idx} className={`hover:bg-indigo-50/20 ${selectedIds.includes(rowId) ? 'bg-indigo-50/30' : ''}`}>
                                                        <td className="px-4 py-3 text-center">
                                                            <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={selectedIds.includes(rowId)} onChange={() => toggleSelection(rowId)} />
                                                        </td>
                                                        {columns.map((col, vIdx) => (
                                                            <td key={vIdx} className="px-4 py-3 whitespace-nowrap text-sm text-slate-600 font-mono border-l border-slate-100 max-w-[200px] truncate" title={String(row[col.name])}>
                                                                {row[col.name] === null ? <span className="text-slate-300 italic">NULL</span> : String(row[col.name])}
                                                            </td>
                                                        ))}
                                                        <td className="px-4 py-3 whitespace-nowrap text-sm text-right border-l border-slate-100 bg-white sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">
                                                            <div className="flex gap-2 justify-end">
                                                                <button onClick={() => openEditor(row)} className="text-indigo-600 hover:text-indigo-800 text-xs flex items-center gap-1"><Edit2 className="w-3 h-3" /> Edit</button>
                                                                <button onClick={() => handleDelete([rowId])} className="text-red-600 hover:text-red-800 text-xs flex items-center gap-1"><Trash2 className="w-3 h-3" /> Del</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        ) : (
                                            <tr><td colSpan={columns.length + 2} className="px-6 py-12 text-center text-slate-500 italic bg-white">This table is empty.</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="inline-block min-w-full align-middle">
                                <table className="min-w-full divide-y divide-slate-200 border-b border-slate-200">
                                    <thead className="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                        <tr>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">#</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Name</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Type</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Collation</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Null</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Default</th>
                                            <th className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Extra</th>
                                            <th className="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-slate-100">
                                        {columns.map((col, idx) => (
                                            <tr key={idx} className="hover:bg-slate-50">
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
                                                <td className="px-4 py-3 text-right">
                                                    <button
                                                        onClick={() => handleDropColumn(col.name)}
                                                        className="text-slate-400 hover:text-red-600 transition-colors flex items-center gap-1 ml-auto text-xs"
                                                        title="Drop Column"
                                                    >
                                                        <Trash2 className="w-3 h-3" /> Drop
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )
                    )}
                </div>

                {mode === 'BROWSE' && (
                    <div className="px-6 py-3 border-t border-slate-200 bg-slate-50 rounded-b-xl flex justify-between items-center text-xs text-slate-500 shrink-0">
                        <div>Total: {pagination.total} row(s)</div>
                        <div className="flex gap-2">
                            <button
                                disabled={pagination.page <= 1 || loading}
                                onClick={() => setPagination(prev => ({ ...prev, page: prev.page - 1 }))}
                                className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-50 disabled:opacity-50"
                            >Prev</button>
                            <span className="py-1">Page {pagination.page}</span>
                            <button
                                disabled={data.length < pagination.limit || loading}
                                onClick={() => setPagination(prev => ({ ...prev, page: prev.page + 1 }))}
                                className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-50 disabled:opacity-50"
                            >Next</button>
                        </div>
                    </div>
                )}
            </div>

            {isEditingItem && (
                <div className="absolute inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                    <div className="bg-white rounded-lg shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 fade-in duration-200">
                        <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h3 className="font-bold text-slate-800">{editTargetRow ? 'Edit' : 'Insert'} Row</h3>
                            <button onClick={() => setIsEditingItem(false)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                        </div>
                        <div className="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                            {columns.map(col => (
                                <div key={col.name} className="space-y-1">
                                    <label className="text-xs font-bold text-slate-500 uppercase">{col.name} <span className="text-indigo-400 font-normal normal-case">({col.type})</span></label>
                                    {col.extra === 'auto_increment' && !editTargetRow ? (
                                        <input type="text" disabled value="(Auto Increment)" className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-slate-500 italic text-sm" />
                                    ) : (
                                        <input
                                            type="text"
                                            value={formData[col.name] !== undefined && formData[col.name] !== null ? formData[col.name] : ''}
                                            onChange={e => setFormData({ ...formData, [col.name]: e.target.value })}
                                            className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-mono text-slate-700"
                                            placeholder={col.default === 'NULL' ? 'NULL' : ''}
                                        />
                                    )}
                                </div>
                            ))}
                        </div>
                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button onClick={() => setIsEditingItem(false)} className="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded font-medium text-sm">Cancel</button>
                            <button onClick={handleSave} disabled={isSaving} className="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded font-medium text-sm shadow-sm flex items-center gap-2">
                                {isSaving && <Loader2 className="w-4 h-4 animate-spin" />} Save
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {isAddingColumn && (
                <AddColumnModal
                    siteId={viewingTable.siteId}
                    tableName={viewingTable.tableName}
                    onClose={() => setIsAddingColumn(false)}
                    onSuccess={() => {
                        setIsAddingColumn(false);
                        fetchData();
                    }}
                />
            )}
        </div>
    );
};
