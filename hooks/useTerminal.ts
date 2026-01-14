import { useState } from 'react';
import { LogEntry, TerminalAction } from '../types';

export const useTerminal = () => {
  const [siteLogs, setSiteLogs] = useState<Record<string, LogEntry[]>>({});
  const [isExecuting, setIsExecuting] = useState(false);

  const addLog = (siteId: string, text: string, type: 'info' | 'error' | 'success' | 'command' = 'info') => {
    const newLog: LogEntry = {
      id: Math.random().toString(36).substr(2, 9),
      timestamp: new Date().toLocaleTimeString([], { hour12: false }),
      text,
      type
    };

    setSiteLogs(prev => ({
      ...prev,
      [siteId]: [...(prev[siteId] || []), newLog]
    }));
  };

  const executeCommand = (siteId: string, action: TerminalAction) => {
    if (isExecuting) return;
    setIsExecuting(true);

    addLog(siteId, action.command, 'command');

    let step = 0;
    const interval = setInterval(() => {
      step++;

      if (action.id === 'npm_install') {
        if (step === 1) addLog(siteId, 'audited 142 packages in 2s');
        if (step === 2) addLog(siteId, 'found 0 vulnerabilities');
        if (step === 3) {
            addLog(siteId, 'Packages installed successfully.', 'success');
            clearInterval(interval);
            setIsExecuting(false);
        }
      } 
      else if (action.id === 'migrate') {
        if (step === 1) addLog(siteId, 'Migrating: 2014_10_12_000000_create_users_table');
        if (step === 2) addLog(siteId, 'Migrated:  2014_10_12_000000_create_users_table (24.12ms)');
        if (step === 3) addLog(siteId, 'Migrating: 2019_08_19_000000_create_failed_jobs_table');
        if (step === 4) addLog(siteId, 'Migrated:  2019_08_19_000000_create_failed_jobs_table (12.04ms)');
        if (step === 5) {
            addLog(siteId, 'Database migration completed.', 'success');
            clearInterval(interval);
            setIsExecuting(false);
        }
      }
      else if (action.id === 'npm_build') {
         if (step === 1) addLog(siteId, 'Creating an optimized production build...');
         if (step === 2) addLog(siteId, 'Compiled successfully.');
         if (step === 3) addLog(siteId, '  Page            Size     First Load JS');
         if (step === 4) addLog(siteId, '  ┌ λ /           5.4 kB   84.2 kB');
         if (step === 5) addLog(siteId, '  └ λ /about      2.1 kB   79.1 kB');
         if (step === 6) {
             addLog(siteId, 'Build finished.', 'success');
             clearInterval(interval);
             setIsExecuting(false);
         }
      }
      else if (action.id === 'npm_start') {
        if (step === 1) addLog(siteId, '> project@1.0.0 start');
        if (step === 2) addLog(siteId, '> node server.js');
        if (step === 3) addLog(siteId, 'ready - started server on 0.0.0.0:3000, url: http://localhost:3000');
        if (step === 4) {
             addLog(siteId, 'Application is running.', 'success');
             clearInterval(interval);
             setIsExecuting(false);
        }
      }
      else {
        if (step === 1) addLog(siteId, 'Processing...');
        if (step === 2) {
            addLog(siteId, 'Operation completed successfully.', 'success');
            clearInterval(interval);
            setIsExecuting(false);
        }
      }

    }, 800);
  };

  return {
    siteLogs,
    isExecuting,
    executeCommand
  };
};