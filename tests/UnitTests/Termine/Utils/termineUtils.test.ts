import {getTerminUpdateFromTemplate, TerminUpdate} from '../../../../src/Termine/Utils/termineUtils';
import {OlzTerminLabelData, OlzTerminTemplateData} from '../../../../src/Api/client/generated_olz_api_types';
import {Entity} from '../../../../src/Utils/generalUtils';

const labels: Entity<OlzTerminLabelData>[] = [
    {
        'id': 1,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'programm', 'name': 'Jahresprogramm', 'details': '', 'icon': null, 'position': 0, 'imageIds': [], 'fileIds': []},
    },
    {
        'id': 2,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'weekend', 'name': 'Weekends', 'details': '', 'icon': null, 'position': 1, 'imageIds': [], 'fileIds': []},
    },
    {
        'id': 3,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'training', 'name': 'Trainings', 'details': '![](./QQ8ZApZjsNSBM2wKrkRQxXZG.jpg) Komm an eines unserer Trainings! [Trainingskonzept als PDF](./6f6novQPv2fjHGzzguXE6nzi.pdf)', 'icon': null, 'position': 2, 'imageIds': ['QQ8ZApZjsNSBM2wKrkRQxXZG.jpg'], 'fileIds': ['6f6novQPv2fjHGzzguXE6nzi.pdf']},
    },
    {
        'id': 4,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'trophy', 'name': 'OLZ-Trophy', 'details': 'Nimm teil an der OLZ Trophy, einer Reihe von OLs f\u00fcr alle Leistungsstufen!', 'icon': 'EM8hA6vye74doeon2RWzZyRf.svg', 'position': 3, 'imageIds': [], 'fileIds': ['EM8hA6vye74doeon2RWzZyRf.svg']},
    },
    {
        'id': 5,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'ol', 'name': 'Wettk\u00e4mpfe', 'details': '', 'icon': null, 'position': 4, 'imageIds': [], 'fileIds': []},
    },
    {
        'id': 6,
        'meta': {'ownerUserId': null, 'ownerRoleId': null, 'onOff': true},
        'data': {'ident': 'club', 'name': 'Vereinsanl\u00e4sse', 'details': '', 'icon': null, 'position': 5, 'imageIds': [], 'fileIds': []},
    },
];

describe('getTerminUpdateFromTemplate', () => {
    const minimalTemplate: OlzTerminTemplateData = {
        startTime: null,
        durationSeconds: null,
        deadlineEarlierSeconds: null,
        deadlineTime: null,
        shouldPromote: false,
        newsletter: false,
        title: '',
        text: '',
        organizerUserId: null,
        types: [],
        locationId: null,
        imageIds: [],
        fileIds: [],
    };
    const minimalTerminUpdate: TerminUpdate = {
        startTime: '',
        endDate: '',
        endTime: '',
        deadline: '',
        shouldPromote: '',
        hasNewsletter: '',
        types: ['', '', '', '', '', ''],
        title: '',
        text: '',
        locationId: null,
        imageIds: [],
        fileIds: [],
    };

    const defaultTemplate: OlzTerminTemplateData = {
        startTime: '18:00',
        durationSeconds: 5 * 60 * 60,
        deadlineEarlierSeconds: 7 * 24 * 60 * 60,
        deadlineTime: '23:59:59',
        shouldPromote: true,
        newsletter: true,
        title: 'test title',
        text: 'test text',
        organizerUserId: 123,
        types: ['programm', 'weekend', 'club'],
        locationId: 123,
        imageIds: ['image__________________1.jpg', 'image__________________2.png'],
        fileIds: ['file___________________1.pdf', 'file___________________2.txt'],
    };
    const deafultTerminUpdate: TerminUpdate = {
        startTime: '18:00',
        endDate: '2020-03-28',
        endTime: '23:00:00',
        deadline: '2020-03-21 23:59:59',
        shouldPromote: 'yes',
        hasNewsletter: 'yes',
        types: ['yes', 'yes', '', '', '', 'yes'],
        title: 'test title',
        text: 'test text',
        locationId: 123,
        imageIds: ['image__________________1.jpg', 'image__________________2.png'],
        fileIds: ['file___________________1.pdf', 'file___________________2.txt'],
    };

    it('works for default case', async () => {
        expect(getTerminUpdateFromTemplate(defaultTemplate, '2020-03-28', '', labels)).toEqual(deafultTerminUpdate);
    });

    it('works for minimal case', async () => {
        expect(getTerminUpdateFromTemplate(minimalTemplate, '2020-03-28', '', labels)).toEqual(minimalTerminUpdate);
    });

    it('works with non-iso startDate', async () => {
        expect(getTerminUpdateFromTemplate(defaultTemplate, '28.3.2020', '', labels)).toEqual(deafultTerminUpdate);
    });

    it('works with invalid startDate', async () => {
        // Uses window.olzIsoNow
        expect(getTerminUpdateFromTemplate(defaultTemplate, 'invalid', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-13', deadline: '2020-03-06 23:59:59',
        });
    });

    it('works with startTime set', async () => {
        expect(getTerminUpdateFromTemplate(defaultTemplate, '2020-03-28', '17:30', labels)).toEqual({
            ...deafultTerminUpdate, startTime: '17:30', endDate: '2020-03-28', endTime: '22:30:00',
        });
    });

    it('works with invalid startTime', async () => {
        expect(getTerminUpdateFromTemplate(defaultTemplate, '2020-03-28', 'invalid', labels)).toEqual(null);
    });

    it('end time is calculated correctly', async () => {
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, durationSeconds: 6 * 60 * 60,
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', endTime: '00:00:00',
        });

        // Daylight saving time boundary
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, durationSeconds: 7.5 * 60 * 60,
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', endTime: '01:30:00',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, durationSeconds: 8.5 * 60 * 60,
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', endTime: '03:30:00',
        });
    });

    it('deadline is calculated correctly without time', async () => {
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 24 * 60 * 60, deadlineTime: null,
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-27 18:00:00',
        });

        // Daylight saving time boundary
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 14.5 * 60 * 60, deadlineTime: null,
        }, '2020-03-29', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', deadline: '2020-03-29 03:30:00',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 15.5 * 60 * 60, deadlineTime: null,
        }, '2020-03-29', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', deadline: '2020-03-29 01:30:00',
        });
    });

    it('deadline is calculated correctly with time', async () => {
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 24 * 60 * 60, deadlineTime: '23:59:59',
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-27 23:59:59',
        });

        // Edge cases rounding
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 24 * 60 * 60, deadlineTime: '06:00:00',
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-27 06:00:00',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 24 * 60 * 60, deadlineTime: '05:59:59',
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-28 05:59:59',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 12 * 60 * 60, deadlineTime: '18:00:00',
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-28 18:00:00',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 12 * 60 * 60, deadlineTime: '18:00:01',
        }, '2020-03-28', '', labels)).toEqual({
            ...deafultTerminUpdate, deadline: '2020-03-27 18:00:01',
        });

        // Daylight saving time boundary
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 14.5 * 60 * 60, deadlineTime: null,
        }, '2020-03-29', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', deadline: '2020-03-29 03:30:00',
        });
        expect(getTerminUpdateFromTemplate({
            ...defaultTemplate, deadlineEarlierSeconds: 15.5 * 60 * 60, deadlineTime: null,
        }, '2020-03-29', '', labels)).toEqual({
            ...deafultTerminUpdate, endDate: '2020-03-29', deadline: '2020-03-29 01:30:00',
        });
    });
});
