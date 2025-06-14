<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Laravel Chatbot</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
            rel="stylesheet"
        />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                @layer theme {
                    :root,
                    :host {
                        --font-sans: 'Instrument Sans',
                            ui-sans-serif,
                            system-ui,
                            sans-serif,
                            'Apple Color Emoji',
                            'Segoe UI Emoji',
                            'Segoe UI Symbol',
                            'Noto Color Emoji';
                        --font-serif: ui-serif,
                            Georgia,
                            Cambria,
                            'Times New Roman',
                            Times,
                            serif;
                        --font-mono: ui-monospace,
                            SFMono-Regular,
                            Menlo,
                            Monaco,
                            Consolas,
                            'Liberation Mono',
                            'Courier New',
                            monospace;
                    }
                }
            </style>
        @endif
    </head>
    <body
        class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col"
    >
        
        <div
            class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none"
                >
                    <h1 class="mb-1 font-medium text-3xl">Let's get started</h1>
                    <p class="mb-2 text-[#706f6c] text-8xl dark:text-[#A1A09A]">
                        Simple ChatBot
                    </p>
                    
                    
                </div>
                <div
                    class="bg-white dark:bg-[#161615] relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden flex flex-col"
                >
                    <div
                        id="chat-messages"
                        class="flex-1 p-6 pb-4 overflow-y-auto"
                        style="min-height: 300px"
                    >
                        <!-- Chat messages will appear here -->
                    </div>
                    <form
                        id="chat-form"
                        class="flex p-4 border-t border-gray-300 dark:border-gray-700"
                        autocomplete="off"
                    >
                        <input
                            type="text"
                            id="chat-input"
                            name="content"
                            placeholder="Type your message..."
                            class="flex-1 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#0a0a0a] text-gray-900 dark:text-gray-100 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#f53003] dark:focus:ring-[#FF4433]"
                            required
                        />
                        <button
                            type="submit"
                            class="ml-4 bg-[#f53003] dark:bg-[#FF4433] hover:bg-[#d12800] dark:hover:bg-[#cc392b] text-white font-semibold rounded-md px-4 py-2 transition-colors duration-200"
                        >
                            Send
                        </button>
                    </form>
                </div>
            </main>
        </div>

        

        <script>
            (() => {
                const chatForm = document.getElementById('chat-form');
                const chatInput = document.getElementById('chat-input');
                const chatMessages = document.getElementById('chat-messages');

                // Generate a random session ID for the chat session
                const sessionId = localStorage.getItem('chat_session_id') || crypto.randomUUID();
                localStorage.setItem('chat_session_id', sessionId);

                // Function to append a message to the chatMessages container
                function appendMessage(message, sender) {
                    const messageElem = document.createElement('div');
                    messageElem.classList.add('mb-4', 'p-3', 'rounded-md', 'max-w-[80%]');
                    if (sender === 'user') {
                        messageElem.classList.add(
                            'bg-[#f53003]',
                            'text-white',
                            'self-end',
                            'rounded-tr-none'
                        );
                    } else {
                        messageElem.classList.add(
                            'bg-gray-200',
                            'dark:bg-gray-700',
                            'text-gray-900',
                            'dark:text-gray-100',
                            'self-start',
                            'rounded-tl-none'
                        );
                    }
                    messageElem.textContent = message;
                    chatMessages.appendChild(messageElem);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                // Handle form submission
                chatForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const userMessage = chatInput.value.trim();
                    if (!userMessage) return;

                    appendMessage(userMessage, 'user');
                    chatInput.value = '';
                    chatInput.disabled = true;

                    try {
                        const response = await fetch('/chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                session_id: sessionId,
                                content: userMessage,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        appendMessage(data.message, 'bot');
                    } catch (error) {
                        appendMessage('Error: Unable to get response from server.', 'bot');
                    } finally {
                        chatInput.disabled = false;
                        chatInput.focus();
                    }
                });
            })();
        </script>
    </body>
</html>
