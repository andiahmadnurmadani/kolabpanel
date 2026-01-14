import React, { useState, useRef, useEffect } from 'react';
import { Card } from '../Shared';
import { Site, FileNode } from '../../types';
import { HardDrive, Upload, Folder, File as FileIcon, Trash2, Edit2, ChevronDown, Download, CornerUpLeft, Home, Plus, Loader2 } from 'lucide-react';
import { useFileSystem } from '../../hooks/useFileSystem';

interface FileManagerProps {
    sites: Site[];
    // We ignore the passed down props from App.tsx mostly, as we use the updated hook internally here for better control
    fileSystem: any; 
    onRename: any;
    onDelete: any;
    onCreateFolder: any;
    onUpload: any;
}

export const FileManager: React.FC<FileManagerProps> = ({ sites }) => {
  const [selectedSiteId, setSelectedSiteId] = useState(sites[0]?.id || '');
  const [currentPath, setCurrentPath] = useState('/');
  const fileInputRef = useRef<HTMLInputElement>(null);
  
  const selectedSite = sites.find(s => s.id === selectedSiteId) || sites[0];

  // Use the new async hook
  const { currentFiles, loadingFiles, fetchFiles, uploadFile, deleteFile, renameFile, createFolder } = useFileSystem(sites);

  useEffect(() => {
      if (selectedSite) {
          fetchFiles(selectedSite.id, currentPath);
      }
  }, [selectedSiteId, currentPath, selectedSite]); // Reload when site or path changes

  const handleNavigate = (folderName: string) => {
      const newPath = currentPath === '/' ? `/${folderName}` : `${currentPath}/${folderName}`;
      setCurrentPath(newPath);
  };

  const handleNavigateUp = () => {
      if (currentPath === '/') return;
      const parts = currentPath.split('/');
      parts.pop(); 
      const newPath = parts.join('/') || '/'; 
      setCurrentPath(newPath);
  };

  const handleBreadcrumbClick = (index: number) => {
      const parts = currentPath.split('/').filter(Boolean);
      const newPath = '/' + parts.slice(0, index + 1).join('/');
      setCurrentPath(newPath);
  };

  const handleCreateFolderClick = async () => {
      const name = prompt("Enter folder name:");
      if (name && selectedSite) {
          await createFolder(selectedSite.id, currentPath, name);
      }
  };

  const handleUploadClick = () => {
      if (fileInputRef.current) {
          fileInputRef.current.click();
      }
  };

  const handleFileChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
      const file = e.target.files?.[0];
      if (file && selectedSite) {
          await uploadFile(selectedSite.id, currentPath, file);
          if (fileInputRef.current) fileInputRef.current.value = '';
      }
  };

  const handleRenameClick = async (e: React.MouseEvent, node: FileNode) => {
      e.stopPropagation();
      e.preventDefault();
      const newName = prompt(`Rename ${node.name} to:`, node.name);
      if (newName && newName !== node.name && selectedSite) {
          await renameFile(selectedSite.id, currentPath, node.name, newName);
      }
  };

  const handleDeleteClick = async (e: React.MouseEvent, node: FileNode) => {
      e.stopPropagation();
      e.preventDefault();
      if (confirm(`Are you sure you want to delete ${node.name}? ${node.type === 'folder' ? 'All contents inside will be permanently deleted.' : ''}`)) {
          if (selectedSite) {
              await deleteFile(selectedSite.id, currentPath, node.name);
          }
      }
  };

  const handleDownloadClick = (e: React.MouseEvent, node: FileNode) => {
      e.stopPropagation();
      // For a real app, this would be an API call to get a download link
      alert("Download feature requires a static file serving endpoint setup in Express.");
  };

  const SiteSelector = () => (
    <div className="relative group">
      <div className="flex items-center gap-2">
        <label className="text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:block">Active Project:</label>
        <div className="relative">
          <select 
            value={selectedSiteId}
            onChange={(e) => {
                setSelectedSiteId(e.target.value);
                setCurrentPath('/'); 
            }}
            className="appearance-none bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-8 py-1.5 cursor-pointer outline-none"
          >
            {sites.map(site => (
              <option key={site.id} value={site.id}>
                {site.name} ({site.framework})
              </option>
            ))}
          </select>
          <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
            <ChevronDown className="h-4 w-4" />
          </div>
        </div>
      </div>
    </div>
  );

  if (!selectedSite) {
      return (
          <Card title="File Manager">
              <div className="text-center py-12 text-slate-500">
                  No sites available. Please deploy a site first.
              </div>
          </Card>
      )
  }

  return (
    <Card 
      title="File Manager" 
      action={<SiteSelector />}
      className="h-[650px] flex flex-col"
    >
      <div className="mb-4 pb-4 border-b border-slate-100 flex flex-col gap-3">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2">
                <button 
                    onClick={handleCreateFolderClick}
                    className="px-3 py-1.5 text-sm bg-white border border-slate-300 text-slate-700 rounded hover:bg-slate-50 transition-colors flex items-center gap-1"
                >
                    <Plus className="w-4 h-4" /> New Folder
                </button>
                <button 
                    onClick={handleUploadClick}
                    className="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center gap-2 transition-colors shadow-sm"
                >
                    <Upload className="w-3 h-3" /> Upload
                </button>
                <input 
                    type="file" 
                    ref={fileInputRef} 
                    className="hidden" 
                    onChange={handleFileChange}
                />
            </div>
            
            {selectedSite && (
            <div className="hidden sm:flex items-center gap-2 text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full font-mono">
                <HardDrive className="w-3 h-3" />
                <span>/ {selectedSite.name} {currentPath === '/' ? '' : currentPath}</span>
            </div>
            )}
          </div>

          <div className="flex items-center gap-1 text-sm bg-slate-50 p-2 rounded-lg border border-slate-200">
              <button 
                onClick={handleNavigateUp} 
                disabled={currentPath === '/'}
                className="p-1 text-slate-500 hover:bg-slate-200 rounded disabled:opacity-30 disabled:hover:bg-transparent"
                title="Go Up"
              >
                  <CornerUpLeft className="w-4 h-4" />
              </button>
              <div className="w-px h-4 bg-slate-300 mx-1"></div>
              <button 
                onClick={() => setCurrentPath('/')}
                className={`flex items-center p-1 rounded hover:bg-slate-200 ${currentPath === '/' ? 'text-slate-900 font-semibold' : 'text-slate-500'}`}
              >
                  <Home className="w-4 h-4" />
              </button>
              {currentPath !== '/' && currentPath.split('/').filter(Boolean).map((part, index, arr) => (
                  <React.Fragment key={index}>
                      <span className="text-slate-400">/</span>
                      <button 
                        onClick={() => handleBreadcrumbClick(index)}
                        className={`px-1 rounded hover:bg-slate-200 hover:text-indigo-600 ${index === arr.length - 1 ? 'font-semibold text-slate-900' : 'text-slate-500'}`}
                      >
                          {part}
                      </button>
                  </React.Fragment>
              ))}
          </div>
      </div>
      
      <div className="flex-1 overflow-y-auto bg-slate-50 rounded-lg border border-slate-200 relative">
        <div className="grid grid-cols-12 gap-4 px-4 py-3 text-xs font-bold text-slate-500 border-b border-slate-200 bg-slate-100/50 uppercase tracking-wider sticky top-0 backdrop-blur-sm z-10">
          <div className="col-span-6">Name</div>
          <div className="col-span-3">Size</div>
          <div className="col-span-3 text-right">Actions</div>
        </div>
        
        {loadingFiles && (
            <div className="absolute inset-0 bg-white/50 z-20 flex items-center justify-center">
                <Loader2 className="w-8 h-8 animate-spin text-indigo-600" />
            </div>
        )}
        
        {currentFiles.length > 0 ? (
          <div className="divide-y divide-slate-100">
            {currentFiles.map((file) => (
              <div 
                key={file.id} 
                className="grid grid-cols-12 gap-4 px-4 py-3 hover:bg-white items-center text-sm group transition-all cursor-pointer hover:shadow-sm"
                onDoubleClick={() => file.type === 'folder' && handleNavigate(file.name)}
              >
                <div className="col-span-6 flex items-center gap-3 text-slate-700 group-hover:text-indigo-700 transition-colors">
                  {file.type === 'folder' ? 
                    <Folder className="w-5 h-5 text-blue-400 fill-blue-50" /> : 
                    <FileIcon className={`w-5 h-5 ${file.name.endsWith('.zip') ? 'text-orange-500' : 'text-slate-400'}`} />
                  }
                  <span className="font-medium truncate">{file.name}</span>
                </div>
                <div className="col-span-3 text-slate-500 font-mono text-xs">{file.size}</div>
                <div className="col-span-3 flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                  {file.type === 'file' && (
                       <button onClick={(e) => handleDownloadClick(e, file)} className="p-1.5 hover:bg-slate-100 text-slate-500 hover:text-indigo-600 rounded transition-colors relative z-20" title="Download"><Download className="w-3.5 h-3.5" /></button>
                  )}
                  <button onClick={(e) => handleRenameClick(e, file)} className="p-1.5 hover:bg-slate-100 text-slate-500 hover:text-indigo-600 rounded transition-colors relative z-20" title="Rename"><Edit2 className="w-3.5 h-3.5" /></button>
                  <button onClick={(e) => handleDeleteClick(e, file)} className="p-1.5 hover:bg-red-50 text-slate-400 hover:text-red-600 rounded transition-colors relative z-20" title="Delete"><Trash2 className="w-3.5 h-3.5" /></button>
                </div>
              </div>
            ))}
          </div>
        ) : (
          !loadingFiles && (
              <div className="flex flex-col items-center justify-center h-full text-slate-400">
                <Folder className="w-12 h-12 mb-2 opacity-20" />
                <p>Folder is empty.</p>
              </div>
          )
        )}
      </div>
      
      <div className="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
        <div>{currentFiles.length} items</div>
        <div>Total Used: {selectedSite.storageUsed} MB</div>
      </div>
    </Card>
  );
};