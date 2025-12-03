import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import AppRoot from './AppRoot.jsx';
import '../css/app.css';

const root = createRoot(document.getElementById('app'));

root.render(
  <React.StrictMode>
    <BrowserRouter>
      <AppRoot />
      <Toaster position="top-right" />
    </BrowserRouter>
  </React.StrictMode>
);
