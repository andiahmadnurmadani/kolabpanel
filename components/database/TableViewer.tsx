import React, { useState } from 'react';
import { Table, List, Settings, Search, RefreshCw, Plus, Trash2, Edit2, Key, X, Save } from 'lucide-react';
import { Database } from 'lucide-react';

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
}

interface TableViewerProps {
    viewingTable: TableViewState;
    data: { columns: ColumnDef[]; data: any[] };
    onClose: () => void;
    onSave: (formData: any, targetIndex: number | null) => void;
    onDelete: (ids: (number | string)[]) => void;
    onRefresh: () => void;
    switchMode: (mode: 'BROWSE' | 'STRUCTURE') => void;
}

export const TableViewer: React.FC<TableViewerProps> = ({ viewingTable, data, onClose, onSave, onDelete, onRefresh, switchMode }) => {
    const [selectedIds, setSelectedIds] = useState<(number | string)[]>([]);
    const [isEditingItem, setIsEditingItem] = useState(false);
    const [editTargetIndex, setEditTargetIndex] = useState<number | null>(null);
    const [formData, setFormData] = useState<any>({});

    const toggleSelection = (id: number | string) => {
        setSelectedIds(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id]);
    };

    const toggleSelectAll = (allIds: (number | string)[]) => {
        setSelectedIds(selectedIds.length === allIds.length ? [] : allIds);
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

    const handleSaveInternal = () => {
        onSave(formData, editTargetIndex);
        setIsEditingItem(false);
        setEditTargetIndex(null);
        setFormData({});
    };

    return (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" onClick={onClose} />
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
                            <button onClick={() => switchMode('BROWSE')} className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${viewingTable.mode === 'BROWSE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}>
                                <List className="w-3.5 h-3.5" /> Browse
                            </button>
                            <button onClick={() => switchMode('STRUCTURE')} className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2 ${viewingTable.mode === 'STRUCTURE' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'}`}>
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
                         <button onClick={() => openEditor()} className="px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded hover:bg-emerald-100 flex items-center gap-1 font-medium transition-colors">
                             <Plus className="w-3 h-3" /> {viewingTable.mode === 'BROWSE' ? 'Insert Row' : 'Add Column'}
                         </button>
                         {selectedIds.length > 0 && (
                             <button onClick={() => onDelete(selectedIds)} className="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded hover:bg-red-100 flex items-center gap-1 font-medium transition-colors animate-in fade-in">
                                 <Trash2 className="w-3 h-3" /> Delete Selected ({selectedIds.length})
                             </button>
                         )}
                     </div>
                     <div className="flex items-center gap-2 text-slate-500">
                        <div className="relative">
                            <Search className="w-3 h-3 absolute left-2 top-1.5 text-slate-400" />
                            <input type="text" placeholder="Search..." className="pl-7 pr-3 py-1 border border-slate-200 rounded text-xs focus:outline-none focus:border-indigo-400 w-32" />
                        </div>
                        <button onClick={onRefresh} className="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-indigo-600"><RefreshCw className="w-3.5 h-3.5" /></button>
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
                                            <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={data.data.length > 0 && selectedIds.length === data.data.length} onChange={() => toggleSelectAll(data.data.map((_, i) => i))} />
                                        </th>
                                        {data.columns.map((col) => (
                                            <th key={col.name} scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider font-mono border-l border-slate-200 whitespace-nowrap">{col.name}</th>
                                        ))}
                                        <th scope="col" className="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase tracking-wider border-l border-slate-200 bg-slate-100 sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">Actions</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-slate-100">
                                    {data.data.length > 0 ? (
                                        data.data.map((row: any, idx) => (
                                            <tr key={idx} className={`hover:bg-indigo-50/20 ${selectedIds.includes(idx) ? 'bg-indigo-50/30' : ''}`}>
                                                <td className="px-4 py-3 text-center">
                                                    <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={selectedIds.includes(idx)} onChange={() => toggleSelection(idx)} />
                                                </td>
                                                {data.columns.map((col, vIdx) => (
                                                    <td key={vIdx} className="px-4 py-3 whitespace-nowrap text-sm text-slate-600 font-mono border-l border-slate-100 max-w-[200px] truncate" title={String(row[col.name])}>
                                                        {row[col.name] === null ? <span className="text-slate-300 italic">NULL</span> : String(row[col.name])}
                                                    </td>
                                                ))}
                                                <td className="px-4 py-3 whitespace-nowrap text-sm text-right border-l border-slate-100 bg-white sticky right-0 shadow-[-5px_0px_10px_rgba(0,0,0,0.02)]">
                                                    <div className="flex gap-2 justify-end">
                                                        <button onClick={() => openEditor(row, idx)} className="text-indigo-600 hover:text-indigo-800 text-xs flex items-center gap-1"><Edit2 className="w-3 h-3" /> Edit</button>
                                                        <button onClick={() => onDelete([idx])} className="text-red-600 hover:text-red-800 text-xs flex items-center gap-1"><Trash2 className="w-3 h-3" /> Del</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr><td colSpan={data.columns.length + 2} className="px-6 py-12 text-center text-slate-500 italic bg-white">This table is empty.</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    ) : (
                        <div className="inline-block min-w-full align-middle">
                            <table className="min-w-full divide-y divide-slate-200 border-b border-slate-200">
                                <thead className="bg-slate-100 sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        <th className="w-12 px-4 py-3 text-center">
                                             <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={data.columns.length > 0 && selectedIds.length === data.columns.length} onChange={() => toggleSelectAll(data.columns.map((_, i) => i))} />
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
                                    {data.columns.map((col, idx) => (
                                        <tr key={idx} className={`hover:bg-slate-50 ${selectedIds.includes(idx) ? 'bg-indigo-50/30' : ''}`}>
                                             <td className="px-4 py-3 text-center">
                                                    <input type="checkbox" className="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked={selectedIds.includes(idx)} onChange={() => toggleSelection(idx)} />
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
                                                    <button onClick={() => onDelete([idx])} className="hover:text-red-600"><Trash2 className="w-3.5 h-3.5" /></button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                 </div>

                 <div className="px-6 py-3 border-t border-slate-200 bg-slate-50 rounded-b-xl flex justify-between items-center text-xs text-slate-500 shrink-0">
                    <div>{viewingTable.mode === 'BROWSE' ? `Total: ${data.data.length} row(s)` : `Total: ${data.columns.length} column(s)`}</div>
                    <div className="font-mono text-[10px] text-slate-400">Query took 0.0001 sec (Memory)</div>
                 </div>
            </div>

            {/* Editor Modal */}
            {isEditingItem && (
                <div className="absolute inset-0 z-[80] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
                     <div className="bg-white rounded-lg shadow-2xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 fade-in duration-200">
                        <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h3 className="font-bold text-slate-800">{editTargetIndex !== null ? 'Edit' : 'Insert'} {viewingTable.mode === 'BROWSE' ? 'Row' : 'Column'}</h3>
                            <button onClick={() => setIsEditingItem(false)} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                        </div>
                        <div className="p-6 max-h-[60vh] overflow-y-auto space-y-4">
                            {viewingTable.mode === 'BROWSE' ? (
                                data.columns.map(col => (
                                    <div key={col.name} className="space-y-1">
                                        <label className="text-xs font-bold text-slate-500 uppercase">{col.name} <span className="text-indigo-400 font-normal normal-case">({col.type})</span></label>
                                        {col.extra === 'auto_increment' && editTargetIndex === null ? (
                                             <input type="text" disabled value="(Auto Increment)" className="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded text-slate-500 italic text-sm" />
                                        ) : (
                                            <input type="text" value={formData[col.name] || ''} onChange={e => setFormData({...formData, [col.name]: e.target.value})} className="w-full px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-mono text-slate-700" placeholder={col.default === 'NULL' ? 'NULL' : ''} />
                                        )}
                                    </div>
                                ))
                            ) : (
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
                                                 <label className="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" checked={formData.null === 'YES'} onChange={e => setFormData({...formData, null: e.target.checked ? 'YES' : 'NO'})} /> Nullable</label>
                                                 <label className="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" checked={formData.key === 'PRI'} onChange={e => setFormData({...formData, key: e.target.checked ? 'PRI' : ''})} /> Primary Key</label>
                                                  <label className="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" checked={formData.extra === 'auto_increment'} onChange={e => setFormData({...formData, extra: e.target.checked ? 'auto_increment' : ''})} /> Auto Increment</label>
                                             </div>
                                        </div>
                                    </div>
                                </>
                            )}
                        </div>
                        <div className="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button onClick={() => setIsEditingItem(false)} className="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded font-medium text-sm">Cancel</button>
                            <button onClick={handleSaveInternal} className="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded font-medium text-sm shadow-sm flex items-center gap-2"><Save className="w-4 h-4" /> Save</button>
                        </div>
                     </div>
                </div>
            )}
        </div>
    );
};
