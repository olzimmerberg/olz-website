import React from 'react';
import {createRoot} from 'react-dom/client';

const reactRoots: {[rootElemId: string]: ReturnType<typeof createRoot>} = {};

export function initReact(
    rootElemId: string,
    reactElem: React.ReactElement,
): boolean {
    const rootElem = document.getElementById(rootElemId);
    if (!rootElem) {
        return false;
    }
    if (reactRoots[rootElemId]) {
        reactRoots[rootElemId].unmount();
    }
    reactRoots[rootElemId] = createRoot(rootElem);
    reactRoots[rootElemId].render(reactElem);
    return false;
}
