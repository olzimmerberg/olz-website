type WindowWithDemos = typeof window & {demos: {[demoId: string]: () => void}};

export function registerDemo(name: string, mainFn: () => void): void {
    const windowWithDemos = window as WindowWithDemos;
    if (!windowWithDemos.demos) {
        windowWithDemos.demos = {};
    }
    windowWithDemos.demos[name] = mainFn;
}
