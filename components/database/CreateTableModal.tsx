import React, { useState } from 'react';
import { X, Plus, Trash2, Loader2, Save } from 'lucide-react';
import { api } from '../../services/api';

interface ColumnDraft {
    name: string;
    type: string;
    length: string;
    default: string;
    isNullable: boolean;
    isPrimaryKey: boolean;
    isAutoIncrement: boolean;
}

interface CreateTableModalProps {
    siteId: string;
    dbName: string;
    onClose: () => void;
    onSuccess: () => void;
}

export const CreateTableModal: React.FC<CreateTableModalProps> = ({ siteId, dbName, onClose, onSuccess }) => {
    const [tableName, setTableName] = useState('');
    const [columns, setColumns] = useState<ColumnDraft[]>([
        { name: 'id', type: 'INT', length: '', default: '', isNullable: false, isPrimaryKey: true, isAutoIncrement: true }
    ]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const addColumn = () => {
        setColumns([...columns, { name: '', type: 'VARCHAR', length: '255', default: '', isNullable: true, isPrimaryKey: false, isAutoIncrement: false }]);
    };

    const removeColumn = (index: number) => {
        setColumns(columns.filter((_, i) => i !== index));
    };

    const updateColumn = (index: number, field: keyof ColumnDraft, value: any) => {
        const newCols = [...columns];
        newCols[index] = { ...newCols[index], [field]: value };
        setColumns(newCols);
    };

    const handleSubmit = async () => {
        if (!tableName) return setError("Table name is required");
        if (columns.length === 0) return setError("At least one column is required");

        setIsSubmitting(true);
        setError(null);

        try {
            // Generate SQL
            const colDefs = columns.map(c => {
                let def = `\`${c.name}\` ${c.type}`;
                if (c.length && !['TEXT', 'DATE', 'DATETIME'].includes(c.type)) {
                    def += `(${c.length})`;
                }

                if (!c.isNullable) def += ' NOT NULL';
                else def += ' NULL';

                if (c.default) {
                    def += ` DEFAULT '${c.default}'`;
                } else if (c.isNullable && c.default === null) {
                    def += ' DEFAULT NULL';
                }

                if (c.isAutoIncrement) def += ' AUTO_INCREMENT';

                return def;
            });

            const pks = columns.filter(c => c.isPrimaryKey).map(c => `\`${c.name}\``);

            let sql = `CREATE TABLE \`${tableName}\` (\n  ${colDefs.join(',\n  ')}`;
            if (pks.length > 0) {
                sql += `,\n  PRIMARY KEY (${pks.join(', ')})`;
            }
            sql += `\n) ENGINE=InnoDB;`;

            await api.database.query(siteId, sql);
            onSuccess();
        } catch (err: any) {
            setError(err.message || "Failed to create table");
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={onClose} />
            <div className="relative w-full max-w-4xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-200">

                {/* Header */}
                <div className="flex justify-between items-center p-6 border-b border-slate-200">
                    <div>
                        <h3 className="text-xl font-bold text-slate-900">Create New Table</h3>
                        <p className="text-sm text-slate-500">Database: <span className="font-mono text-indigo-600">{dbName}</span></p>
                    </div>
                    <button onClick={onClose} className="text-slate-400 hover:text-slate-600 transition-colors"><X className="w-6 h-6" /></button>
                </div>

                {/* Body */}
                <div className="p-6 overflow-y-auto flex-1">
                    {error && (
                        <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg flex items-center gap-2">
                            <span className="font-bold">Error:</span> {error}
                        </div>
                    )}

                    <div className="mb-6">
                        <label className="block text-sm font-medium text-slate-700 mb-2">Table Name</label>
                        <input
                            type="text"
                            value={tableName}
                            onChange={(e) => setTableName(e.target.value)}
                            className="w-full max-w-md px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                            placeholder="e.g. users, products, orders"
                            autoFocus
                        />
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center justify-between mb-2">
                            <label className="text-sm font-medium text-slate-700">Columns</label>
                            <button onClick={addColumn} className="text-xs flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium">
                                <Plus className="w-3 h-3" /> Add Column
                            </button>
                        </div>

                        <div className="border border-slate-200 rounded-lg overflow-hidden">
                            <table className="min-w-full divide-y divide-slate-200 text-sm">
                                <thead className="bg-slate-50 text-slate-500">
                                    <tr>
                                        <th className="px-3 py-2 text-left font-medium w-6"></th>
                                        <th className="px-3 py-2 text-left font-medium">Name</th>
                                        <th className="px-3 py-2 text-left font-medium">Type</th>
                                        <th className="px-3 py-2 text-left font-medium w-20">Length</th>
                                        <th className="px-3 py-2 text-left font-medium">Default</th>
                                        <th className="px-3 py-2 text-center font-medium w-12" title="Nullable">Null</th>
                                        <th className="px-3 py-2 text-center font-medium w-12" title="Primary Key">PK</th>
                                        <th className="px-3 py-2 text-center font-medium w-12" title="Auto Increment">AI</th>
                                        <th className="px-3 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 bg-white">
                                    {columns.map((col, idx) => (
                                        <tr key={idx} className="group hover:bg-slate-50">
                                            <td className="px-3 py-2 text-slate-400 text-xs text-center">{idx + 1}</td>
                                            <td className="px-3 py-2">
                                                <input
                                                    type="text"
                                                    value={col.name}
                                                    onChange={(e) => updateColumn(idx, 'name', e.target.value)}
                                                    className="w-full px-2 py-1 border border-slate-300 rounded text-sm font-mono focus:border-indigo-500 outline-none"
                                                    placeholder="col_name"
                                                />
                                            </td>
                                            <td className="px-3 py-2">
                                                <select
                                                    value={col.type}
                                                    onChange={(e) => updateColumn(idx, 'type', e.target.value)}
                                                    className="w-full px-2 py-1 border border-slate-300 rounded text-sm focus:border-indigo-500 outline-none"
                                                >
                                                    <option value="INT">INT</option>
                                                    <option value="VARCHAR">VARCHAR</option>
                                                    <option value="TEXT">TEXT</option>
                                                    <option value="DATE">DATE</option>
                                                    <option value="DATETIME">DATETIME</option>
                                                    <option value="BOOLEAN">BOOLEAN</option>
                                                    <option value="DECIMAL">DECIMAL</option>
                                                    <option value="BIGINT">BIGINT</option>
                                                </select>
                                            </td>
                                            <td className="px-3 py-2">
                                                <input
                                                    type="text"
                                                    value={col.length}
                                                    onChange={(e) => updateColumn(idx, 'length', e.target.value)}
                                                    disabled={['TEXT', 'DATE', 'DATETIME', 'BOOLEAN'].includes(col.type)}
                                                    className="w-full px-2 py-1 border border-slate-300 rounded text-sm focus:border-indigo-500 outline-none disabled:bg-slate-100 disabled:text-slate-400"
                                                    placeholder={col.type === 'VARCHAR' ? '255' : ''}
                                                />
                                            </td>
                                            <td className="px-3 py-2">
                                                <input
                                                    type="text"
                                                    value={col.default}
                                                    onChange={(e) => updateColumn(idx, 'default', e.target.value)}
                                                    className="w-full px-2 py-1 border border-slate-300 rounded text-sm focus:border-indigo-500 outline-none"
                                                    placeholder="NULL"
                                                />
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={col.isNullable}
                                                    onChange={(e) => updateColumn(idx, 'isNullable', e.target.checked)}
                                                    className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                />
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={col.isPrimaryKey}
                                                    onChange={(e) => updateColumn(idx, 'isPrimaryKey', e.target.checked)}
                                                    className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                />
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                <input
                                                    type="checkbox"
                                                    checked={col.isAutoIncrement}
                                                    onChange={(e) => updateColumn(idx, 'isAutoIncrement', e.target.checked)}
                                                    disabled={!['INT', 'BIGINT'].includes(col.type)}
                                                    className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-50"
                                                />
                                            </td>
                                            <td className="px-3 py-2 text-center">
                                                <button onClick={() => removeColumn(idx)} className="text-slate-400 hover:text-red-500 transition-colors" disabled={columns.length === 1}>
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <div className="p-6 border-t border-slate-200 bg-slate-50 rounded-b-xl flex justify-end gap-3">
                    <button onClick={onClose} disabled={isSubmitting} className="px-4 py-2 text-slate-600 hover:bg-white border border-transparent hover:border-slate-200 rounded-lg font-medium text-sm transition-all">Cancel</button>
                    <button onClick={handleSubmit} disabled={isSubmitting} className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm shadow-sm transition-colors flex items-center gap-2">
                        {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                        Create Table
                    </button>
                </div>
            </div>
        </div>
    );
};
