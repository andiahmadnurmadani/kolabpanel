import React, { useState } from 'react';
import { X, Loader2, Save } from 'lucide-react';
import { api } from '../../services/api';

interface AddColumnModalProps {
    siteId: string;
    tableName: string;
    onClose: () => void;
    onSuccess: () => void;
}

export const AddColumnModal: React.FC<AddColumnModalProps> = ({ siteId, tableName, onClose, onSuccess }) => {
    // Single column definition state
    const [name, setName] = useState('');
    const [type, setType] = useState('VARCHAR');
    const [length, setLength] = useState('255');
    const [defaultValue, setDefaultValue] = useState('');
    const [isNullable, setIsNullable] = useState(true);
    const [placeAfter, setPlaceAfter] = useState('LAST'); // TODO: Support AFTER specific_column if we fetch col list? For now just append.

    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const handleSubmit = async () => {
        if (!name) return setError("Column name is required");

        setIsSubmitting(true);
        setError(null);

        try {
            // Generate SQL: ALTER TABLE `tbl` ADD `col` TYPE(LEN) ...
            let def = `\`${name}\` ${type}`;
            if (length && !['TEXT', 'DATE', 'DATETIME', 'BOOLEAN', 'INT', 'BIGINT'].includes(type) || (type === 'VARCHAR')) {
                // Logic for length validity depends on type, simplistically apply if user typed it and type allows
                def += `(${length})`;
            }

            if (!isNullable) def += ' NOT NULL';
            else def += ' NULL';

            if (defaultValue) {
                def += ` DEFAULT '${defaultValue}'`;
            } else if (isNullable && defaultValue === '' && type !== 'TEXT') {
                // If default is empty string, do we mean DEFAULT NULL or DEFAULT ''? 
                // Let's assume user leaving it blank means Default NULL (if nullable) or No Default.
                // Strict SQL mode might complain if not nullable and no default.
                // Simplify: If user types nothing, don't add DEFAULT clause unless specific checkbox?
                // For now: omit DEFAULT clause if empty string.
            }

            const sql = `ALTER TABLE \`${tableName}\` ADD ${def};`;

            await api.database.query(siteId, sql);
            onSuccess();
        } catch (err: any) {
            setError(err.message || "Failed to add column");
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[70] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onClick={onClose} />
            <div className="relative w-full max-w-lg bg-white rounded-xl shadow-2xl p-6 animate-in fade-in zoom-in-95 duration-200">

                <div className="flex justify-between items-start mb-6">
                    <div>
                        <h3 className="text-lg font-bold text-slate-900">Add Column</h3>
                        <p className="text-sm text-slate-500">Table: <span className="font-mono text-indigo-600">{tableName}</span></p>
                    </div>
                    <button onClick={onClose} className="text-slate-400 hover:text-slate-600"><X className="w-5 h-5" /></button>
                </div>

                {error && (
                    <div className="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                        <span className="font-bold">Error:</span> {error}
                    </div>
                )}

                <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="col-span-2">
                            <label className="block text-sm font-medium text-slate-700 mb-1">Column Name</label>
                            <input
                                type="text"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-sm"
                                placeholder="new_column_name"
                                autoFocus
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Type</label>
                            <select
                                value={type}
                                onChange={(e) => setType(e.target.value)}
                                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
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
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Length/Values</label>
                            <input
                                type="text"
                                value={length}
                                onChange={(e) => setLength(e.target.value)}
                                disabled={['TEXT', 'DATE', 'DATETIME', 'BOOLEAN', 'INT', 'BIGINT'].includes(type)}
                                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm disabled:bg-slate-100 disabled:text-slate-400"
                                placeholder={type === 'VARCHAR' ? '255' : ''}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Default Value</label>
                            <input
                                type="text"
                                value={defaultValue}
                                onChange={(e) => setDefaultValue(e.target.value)}
                                className="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                                placeholder="NULL"
                            />
                        </div>

                        <div className="flex items-center h-full pt-6">
                            <label className="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={isNullable}
                                    onChange={(e) => setIsNullable(e.target.checked)}
                                    className="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span className="text-sm font-medium text-slate-700">Nullable</span>
                            </label>
                        </div>
                    </div>

                    <div className="pt-4 flex justify-end gap-3 border-t border-slate-100 mt-4">
                        <button onClick={onClose} disabled={isSubmitting} className="px-4 py-2 text-slate-600 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                        <button onClick={handleSubmit} disabled={isSubmitting} className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm shadow-sm transition-colors flex items-center gap-2">
                            {isSubmitting ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                            Add Column
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};
