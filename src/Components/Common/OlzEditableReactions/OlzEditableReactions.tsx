import React from 'react';
import {codeHref, user} from '../../../Utils/constants';
import {olzCustomReaction} from '../OlzCustomReactionDialog/OlzCustomReactionDialog';
import {initOlzUserInfoModal} from '../../../Users/Components/OlzUserInfoModal/OlzUserInfoModal';

interface OlzReaction {
    userId: number;
    name: string | null;
    emoji: string;
}

interface OlzEditableReactionsProps {
    defaultEmojis?: Array<string>,
    listFn: () => Promise<Array<OlzReaction>>,
    toggleFn: (emoji: string) => Promise<OlzReaction | null>,
}

export const OlzEditableReactions = (props: OlzEditableReactionsProps): React.ReactElement => {
    const [reactions, setReactions] = React.useState<Array<OlzReaction> | null>(null);

    React.useEffect(() => {
        props.listFn().then((newReactions) => {
            setReactions(newReactions);
        });
    }, []);

    const toggleReaction = async (emoji: string) => {
        if (reactions === null) {
            return;
        }
        try {
            const result = await props.toggleFn(emoji);
            if (result === null) {
                setReactions(reactions.filter((reaction) =>
                    reaction.userId !== user.id || reaction.emoji !== emoji));
            } else {
                setReactions([...reactions, result]);
            }
        } catch {
            // ignore
        }
    };

    React.useEffect(() => {
        const onHashChange = () => {
            const match = /^#react-(.+)$/.exec(location.hash);
            if (!match) {
                return;
            }
            const emoji = decodeURIComponent(match[1]);
            toggleReaction(emoji);
            location.hash = '';
        };
        window.addEventListener('hashchange', onHashChange);
        return () => {
            window.removeEventListener('hashchange', onHashChange);
        };
    }, [reactions]);

    if (reactions === null) {
        return (<></>);
    }

    const countByEmoji: {[emoji: string]: number} = {};
    (props.defaultEmojis ?? []).forEach((emoji) => { countByEmoji[emoji] = 0; });
    const isActiveByEmoji: {[emoji: string]: boolean} = {};
    const nameByUser: {[userId: number]: string | null} = {};
    const emojisByUser: {[userId: number]: Array<string>} = {};
    reactions.forEach((reaction) => {
        countByEmoji[reaction.emoji] ??= 0;
        countByEmoji[reaction.emoji]++;
        nameByUser[reaction.userId] = reaction.name;
        emojisByUser[reaction.userId] ??= [];
        emojisByUser[reaction.userId].push(reaction.emoji);
        if (reaction.userId === user.id) {
            isActiveByEmoji[reaction.emoji] = true;
        }
    });
    const orderedEmojis = Object.keys(countByEmoji);
    orderedEmojis.sort((a, b) => countByEmoji[b] - countByEmoji[a]);
    const emojiButtons = orderedEmojis.map((emoji) => {
        const isActive = isActiveByEmoji[emoji] ?? false;
        const activeClass = isActive ? ' active' : '';
        if (!user.id) {
            return (
                <a
                    href='#login-dialog'
                    className={`reaction${activeClass}`}
                    key={emoji}
                >
                    {emoji} {countByEmoji[emoji]}
                </a>
            );
        }
        return (
            <a
                href='#'
                onClick={() => toggleReaction(emoji)}
                className={`reaction${activeClass}`}
                key={emoji}
            >
                {emoji} {countByEmoji[emoji]}
            </a>
        );
    });

    const addButton = user.id ? (
        <button
            id='add-reaction-button'
            className='btn btn-sm btn-secondary'
            onClick={() => olzCustomReaction((emoji) => toggleReaction(emoji))}
            key='add-button'
        >
            <img src={`${codeHref}assets/icns/new_white_16.svg`} className='noborder' />
        </button>
    ) : null;

    const userOverview = [];
    if (user.id) {
        for (const userId in emojisByUser) {
            const emojis = emojisByUser[userId];
            emojis.sort((a, b) => countByEmoji[b] - countByEmoji[a]);
            userOverview.push(<div key={`user-${userId}`}>
                <a
                    href='#'
                    onClick={() => initOlzUserInfoModal(Number(userId))}
                    className='olz-user-info-modal-trigger name'
                >
                    {nameByUser[userId] ?? '?'}
                </a>
                : {emojis.join(' ')}
            </div>);
        }

        document.querySelectorAll('a[href^=\'#react-\']').forEach((elem) => {
            elem.classList.remove('active');
        });
        (emojisByUser[user.id ?? 0] ?? []).forEach((emoji) => {
            const selector = `a[href^='#react-${encodeURIComponent(emoji)}']`;
            document.querySelectorAll(selector).forEach((elem) => {
                elem.classList.add('active');
            });
        });
    }

    return (<>
        <div className='reactions'>
            {emojiButtons} {addButton}
        </div>
        {userOverview}
    </>);
};
