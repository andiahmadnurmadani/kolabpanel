import { useState } from 'react';
import { LogEntry, TerminalAction } from '../types';
import { api } from '../services/api';

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

  // Helper function untuk strip ANSI color codes
  const stripAnsiCodes = (str: string): string => {
    return str.replace(/\x1b\[[0-9;]*m/g, '');
  };

  const executeCommand = async (siteId: string, action: TerminalAction) => {
    if (isExecuting) return;
    setIsExecuting(true);

    addLog(siteId, action.command, 'command');

    try {
      const token = localStorage.getItem('kp_token');
      
      const response = await fetch(`http://localhost:5000/api/sites/${siteId}/execute-stream`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ command: action.command })
      });

      if (!response.body) {
        throw new Error('No response body');
      }

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let buffer = '';

      while (true) {
        const { done, value } = await reader.read();
        
        if (done) break;

        buffer += decoder.decode(value, { stream: true });
        const lines = buffer.split('\n\n');
        buffer = lines.pop() || '';

        for (const line of lines) {
          if (!line.trim()) continue;
          
          const eventMatch = line.match(/^event: (.+)$/m);
          const dataMatch = line.match(/^data: (.+)$/m);
          
          if (eventMatch && dataMatch) {
            const event = eventMatch[1];
            const data = JSON.parse(dataMatch[1]);
            
            if (event === 'log') {
              const cleanText = stripAnsiCodes(data.text);
              if (data.type === 'stderr') {
                addLog(siteId, cleanText, 'error');
              } else {
                addLog(siteId, cleanText, 'info');
              }
            } else if (event === 'exit') {
              if (data.code === 0) {
                addLog(siteId, `✓ Command completed successfully`, 'success');
              } else {
                addLog(siteId, `✗ Command exited with code ${data.code}`, 'error');
              }
            } else if (event === 'error') {
              addLog(siteId, `Error: ${data.message}`, 'error');
            }
          }
        }
      }

    } catch (error: any) {
      addLog(siteId, `Error: ${error.message || 'Failed to execute command'}`, 'error');
    } finally {
      setIsExecuting(false);
    }
  };

  return {
    siteLogs,
    isExecuting,
    executeCommand
  };
};