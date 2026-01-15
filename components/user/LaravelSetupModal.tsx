import React, { useState } from 'react';
import { X, Terminal, Loader2, CheckCircle, AlertCircle, Package, Wrench, Database, Hammer } from 'lucide-react';

interface LaravelSetupModalProps {
    siteId: string;
    siteName: string;
    onClose: () => void;
}

export const LaravelSetupModal: React.FC<LaravelSetupModalProps> = ({ siteId, siteName, onClose }) => {
    const [selectedSteps, setSelectedSteps] = useState<string[]>(['composer', 'npm', 'storage-link', 'build']);
    const [isRunning, setIsRunning] = useState(false);
    const [outputs, setOutputs] = useState<any[]>([]);
    const [currentStep, setCurrentStep] = useState('');

    const availableSteps = [
        { id: 'composer', label: 'Composer Install', icon: Package, description: 'Install PHP dependencies' },
        { id: 'npm', label: 'NPM Install', icon: Package, description: 'Install Node.js dependencies' },
        { id: 'storage-link', label: 'Storage Link', icon: Wrench, description: 'Create storage symlink' },
        { id: 'build', label: 'Build Assets', icon: Hammer, description: 'Compile frontend assets' },
        { id: 'cache-clear', label: 'Clear Cache', icon: Wrench, description: 'Clear all Laravel caches' },
    ];

    const toggleStep = (stepId: string) => {
        if (selectedSteps.includes(stepId)) {
            setSelectedSteps(selectedSteps.filter(s => s !== stepId));
        } else {
            setSelectedSteps([...selectedSteps, stepId]);
        }
    };

    const handleRun = async () => {
        if (selectedSteps.length === 0) {
            alert('Please select at least one step');
            return;
        }

        setIsRunning(true);
        setOutputs([]);
        setCurrentStep('Connecting to server...');

        try {
            const token = localStorage.getItem('kp_token');
            const res = await fetch(`http://localhost:5000/api/sites/${siteId}/laravel-setup`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ steps: selectedSteps })
            });

            const result = await res.json();

            if (!res.ok) {
                throw new Error(result.message || 'Setup failed');
            }

            setOutputs(result.output || []);
            setCurrentStep('Completed!');
        } catch (err: any) {
            setOutputs([{ command: 'Error', stderr: err.message, exitCode: 1, success: false }]);
            setCurrentStep('Failed');
        } finally {
            setIsRunning(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" onClick={onClose} />
            <div className="relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                
                {/* Header */}
                <div className="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-gradient-to-r from-indigo-50 to-blue-50">
                    <div>
                        <h3 className="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <Terminal className="w-5 h-5 text-indigo-600" />
                            Laravel Setup - {siteName}
                        </h3>
                        <p className="text-xs text-slate-500 mt-0.5">Execute setup commands via SSH</p>
                    </div>
                    <button onClick={onClose} disabled={isRunning} className="p-2 hover:bg-white rounded-full transition-colors">
                        <X className="w-5 h-5 text-slate-500" />
                    </button>
                </div>

                <div className="flex-1 overflow-y-auto p-6">
                    {/* Step Selection */}
                    {!isRunning && outputs.length === 0 && (
                        <div className="space-y-3">
                            <h4 className="font-bold text-slate-800 text-sm mb-3">Select Steps to Execute:</h4>
                            {availableSteps.map(step => {
                                const Icon = step.icon;
                                const isSelected = selectedSteps.includes(step.id);
                                return (
                                    <label
                                        key={step.id}
                                        className={`flex items-center gap-3 p-4 rounded-lg border-2 cursor-pointer transition-all ${
                                            isSelected 
                                                ? 'border-indigo-500 bg-indigo-50' 
                                                : 'border-slate-200 hover:border-slate-300 bg-white'
                                        }`}
                                    >
                                        <input
                                            type="checkbox"
                                            checked={isSelected}
                                            onChange={() => toggleStep(step.id)}
                                            className="w-5 h-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500"
                                        />
                                        <Icon className={`w-5 h-5 ${isSelected ? 'text-indigo-600' : 'text-slate-400'}`} />
                                        <div className="flex-1">
                                            <div className="font-semibold text-slate-900 text-sm">{step.label}</div>
                                            <div className="text-xs text-slate-500">{step.description}</div>
                                        </div>
                                    </label>
                                );
                            })}
                        </div>
                    )}

                    {/* Running Status */}
                    {isRunning && (
                        <div className="flex flex-col items-center justify-center py-12">
                            <Loader2 className="w-12 h-12 text-indigo-600 animate-spin mb-4" />
                            <p className="text-slate-600 font-medium">{currentStep}</p>
                        </div>
                    )}

                    {/* Output Display */}
                    {outputs.length > 0 && (
                        <div className="space-y-4">
                            <h4 className="font-bold text-slate-800 text-sm">Execution Results:</h4>
                            {outputs.map((out, idx) => (
                                <div key={idx} className={`p-4 rounded-lg border ${out.success ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50'}`}>
                                    <div className="flex items-center gap-2 mb-2">
                                        {out.success ? (
                                            <CheckCircle className="w-4 h-4 text-emerald-600" />
                                        ) : (
                                            <AlertCircle className="w-4 h-4 text-red-600" />
                                        )}
                                        <span className="font-mono text-xs text-slate-600">{out.command}</span>
                                        <span className={`ml-auto text-xs font-bold ${out.success ? 'text-emerald-600' : 'text-red-600'}`}>
                                            Exit: {out.exitCode}
                                        </span>
                                    </div>
                                    {out.stdout && (
                                        <pre className="text-xs bg-slate-900 text-green-400 p-3 rounded overflow-x-auto font-mono whitespace-pre-wrap">
{out.stdout}
                                        </pre>
                                    )}
                                    {out.stderr && (
                                        <pre className="text-xs bg-slate-900 text-red-400 p-3 rounded overflow-x-auto font-mono whitespace-pre-wrap mt-2">
{out.stderr}
                                        </pre>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Footer */}
                <div className="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                    <button
                        onClick={onClose}
                        disabled={isRunning}
                        className="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium text-sm transition-colors"
                    >
                        {outputs.length > 0 ? 'Close' : 'Cancel'}
                    </button>
                    {outputs.length === 0 && (
                        <button
                            onClick={handleRun}
                            disabled={isRunning || selectedSteps.length === 0}
                            className="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium text-sm hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-md flex items-center gap-2"
                        >
                            {isRunning ? (
                                <><Loader2 className="w-4 h-4 animate-spin" /> Running...</>
                            ) : (
                                <><Terminal className="w-4 h-4" /> Run Setup</>
                            )}
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
};
